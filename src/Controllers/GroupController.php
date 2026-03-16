<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class GroupController
{
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::getInstance();
    }

    private function requireAuth(): int
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        return (int) $_SESSION['user_id'];
    }

    private function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Help function to check role. Returns 'owner', 'admin', 'elder', 'member' or false
    private function getUserRole($groupId, $userId)
    {
        $stmt = $this->db->prepare("SELECT role FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$groupId, $userId]);
        $row = $stmt->fetch();
        return $row ? $row['role'] : false;
    }

    // POST /api/groups/create
    public function createGroup()
    {
        $myId = $this->requireAuth();
        $name = trim($_POST['name'] ?? '');
        $isPrivate = (isset($_POST['is_private']) && $_POST['is_private'] == '1') ? 1 : 0;
        $desc = trim($_POST['description'] ?? '');
        $adminOnly = (isset($_POST['admin_only']) && $_POST['admin_only'] == '1') ? 1 : 0;
        $memberIds = $_POST['member_ids'] ?? [];

        if (empty($name)) {
            $this->json(['error' => 'Group name is required.']);
        }

        $chatPermission = $adminOnly ? 'admin' : 'member';

        try {
            $this->db->beginTransaction();

            // Try with chat_permission column first, fall back without it
            try {
                $stmt = $this->db->prepare("INSERT INTO groups (owner_id, name, description, is_private, chat_permission) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$myId, $name, $desc, $isPrivate, $chatPermission]);
            } catch (\Exception $colErr) {
                // Column might not exist yet, fall back
                $stmt = $this->db->prepare("INSERT INTO groups (owner_id, name, description, is_private) VALUES (?, ?, ?, ?)");
                $stmt->execute([$myId, $name, $desc, $isPrivate]);
            }
            $groupId = $this->db->lastInsertId();

            // Insert owner as member
            $stmt = $this->db->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'owner')");
            $stmt->execute([$groupId, $myId]);
            
            // Create default 'general' subgroup
            $stmt = $this->db->prepare("INSERT INTO subgroups (group_id, name) VALUES (?, 'general')");
            $stmt->execute([$groupId]);

            // Add invited members
            if (is_array($memberIds) && count($memberIds) > 0) {
                $addStmt = $this->db->prepare("INSERT OR IGNORE INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')");
                foreach ($memberIds as $memberId) {
                    $memberId = (int)$memberId;
                    if ($memberId > 0 && $memberId !== $myId) {
                        $addStmt->execute([$groupId, $memberId]);
                    }
                }
            }

            $this->db->commit();
            $this->json(['success' => true, 'group_id' => $groupId]);
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->json(['error' => 'Failed to create group.', 'details' => $e->getMessage()]);
        }
    }

    // POST /api/groups/members/add
    public function addMember()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        $userIdToAdd = (int)($_POST['user_id'] ?? 0);

        if (!$groupId || !$userIdToAdd) $this->json(['error' => 'Invalid parameters.']);

        $myRole = $this->getUserRole($groupId, $myId);
        if (!in_array($myRole, ['owner', 'admin'])) {
            $this->json(['error' => 'Only admins can add members.']);
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')");
            $stmt->execute([$groupId, $userIdToAdd]);
            $this->json(['success' => true]);
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $this->json(['error' => 'User is already a member.']);
            }
            $this->json(['error' => 'Failed to add member.']);
        }
    }

    // POST /api/groups/members/remove
    public function removeMember()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        $userIdToRemove = (int)($_POST['user_id'] ?? 0);

        if (!$groupId || !$userIdToRemove) $this->json(['error' => 'Invalid parameters.']);

        if ($myId === $userIdToRemove) {
            // User leaving voluntarily
            $role = $this->getUserRole($groupId, $myId);
            if ($role === 'owner') {
                $this->json(['error' => 'Owner cannot leave group without deleting or transferring ownership.']);
            }
        } else {
            // Being kicked
            $myRole = $this->getUserRole($groupId, $myId);
            $targetRole = $this->getUserRole($groupId, $userIdToRemove);
            
            if (!in_array($myRole, ['owner', 'admin'])) $this->json(['error' => 'Not enough permissions.']);
            if ($targetRole === 'owner') $this->json(['error' => 'Cannot kick the owner.']);
            if ($myRole === 'admin' && $targetRole === 'admin') $this->json(['error' => 'Admins cannot kick other admins.']);
        }

        $stmt = $this->db->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$groupId, $userIdToRemove]);

        $this->json(['success' => true]);
    }

    // POST /api/groups/members/role
    public function updateRole()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        $targetUserId = (int)($_POST['user_id'] ?? 0);
        $newRole = $_POST['role'] ?? 'member';

        if (!in_array($newRole, ['admin', 'elder', 'member'])) $this->json(['error' => 'Invalid role.']);
        
        $myRole = $this->getUserRole($groupId, $myId);
        $targetRole = $this->getUserRole($groupId, $targetUserId);

        if ($myRole !== 'owner' && $myRole !== 'admin') $this->json(['error' => 'Not enough permissions.']);
        if ($targetRole === 'owner') $this->json(['error' => 'Cannot change owners role.']);
        if ($myRole === 'admin' && $newRole === 'admin') $this->json(['error' => 'Only Owner can promote to Admin.']);
        if ($myRole === 'admin' && $targetRole === 'admin') $this->json(['error' => 'Admins cannot demote other admins.']);

        $stmt = $this->db->prepare("UPDATE group_members SET role = ? WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$newRole, $groupId, $targetUserId]);
        $this->json(['success' => true]);
    }

    // POST /api/groups/edit
    public function editGroup()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        
        $myRole = $this->getUserRole($groupId, $myId);
        if (!in_array($myRole, ['owner', 'admin'])) {
            $this->json(['error' => 'Only admins can edit the group profile.']);
        }

        $updates = [];
        $params = [];

        if (isset($_POST['name'])) {
            $updates[] = "name = ?"; $params[] = trim($_POST['name']);
        }
        if (isset($_POST['description'])) {
            $updates[] = "description = ?"; $params[] = trim($_POST['description']);
        }
        if (isset($_POST['chat_permission']) && in_array($_POST['chat_permission'], ['member', 'elder', 'admin'])) {
            $updates[] = "chat_permission = ?"; $params[] = $_POST['chat_permission'];
        }
        if (isset($_POST['status']) && in_array($_POST['status'], ['open', 'closed'])) {
            $updates[] = "status = ?"; $params[] = $_POST['status'];
        }
        if (isset($_POST['join_mode']) && in_array($_POST['join_mode'], ['free', 'approval'])) {
            $updates[] = "join_mode = ?"; $params[] = $_POST['join_mode'];
        }
        if (isset($_POST['elder_msg_per_hour'])) {
            $updates[] = "elder_msg_per_hour = ?"; $params[] = max(1, (int)$_POST['elder_msg_per_hour']);
        }
        if (isset($_POST['elder_max_chars'])) {
            $updates[] = "elder_max_chars = ?"; $params[] = max(10, (int)$_POST['elder_max_chars']);
        }
        if (isset($_POST['is_private'])) {
            $updates[] = "is_private = ?"; $params[] = (int)$_POST['is_private'];
        }

        if (empty($updates)) $this->json(['success' => true]);

        $params[] = $groupId;
        $sql = "UPDATE groups SET " . implode(", ", $updates) . " WHERE id = ?";
        
        $this->db->prepare($sql)->execute($params);
        $this->json(['success' => true]);
    }

    // POST /api/groups/subgroups/create
    public function createSubgroup()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if (!$groupId || empty($name)) $this->json(['error' => 'Invalid parameters.']);

        $myRole = $this->getUserRole($groupId, $myId);
        if (!in_array($myRole, ['owner', 'admin'])) {
            $this->json(['error' => 'Only admins can create subgroups.']);
        }

        $stmt = $this->db->prepare("INSERT INTO subgroups (group_id, name) VALUES (?, ?)");
        $stmt->execute([$groupId, $name]);
        $this->json(['success' => true, 'subgroup_id' => $this->db->lastInsertId()]);
    }

    // POST /api/groups/delete
    public function deleteGroup()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);

        $myRole = $this->getUserRole($groupId, $myId);
        if ($myRole !== 'owner') {
            $this->json(['error' => 'Only the owner can delete the group.']);
        }

        $this->db->prepare("DELETE FROM groups WHERE id = ?")->execute([$groupId]);
        $this->json(['success' => true]);
    }

    // GET /api/groups/search
    public function searchGroups()
    {
        $query = trim($_GET['q'] ?? '');
        if (strlen($query) < 2) $this->json(['groups' => []]);

        $stmt = $this->db->prepare("SELECT id, name, description, avatar_url FROM groups WHERE is_private = 0 AND name LIKE ? LIMIT 10");
        $stmt->execute(["%{$query}%"]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->json(['groups' => $groups]);
    }

    // GET /api/groups/info?group_id=X
    public function getGroupInfo()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_GET['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);

        // Get group details
        $stmt = $this->db->prepare("SELECT id, owner_id, name, description, avatar_url, cover_url, is_private, chat_permission, status, join_mode, elder_msg_per_hour, elder_max_chars, invite_token, created_at FROM groups WHERE id = ?");
        $stmt->execute([$groupId]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$group) $this->json(['error' => 'Group not found.']);

        // Check membership
        $myRole = $this->getUserRole($groupId, $myId);
        if (!$myRole && $group['is_private']) {
            $this->json(['error' => 'You are not a member of this private group.']);
        }

        // Get members with user info
        $stmt = $this->db->prepare("
            SELECT gm.user_id, gm.role, gm.joined_at, u.username, u.avatar_url, u.status_message
            FROM group_members gm
            JOIN users u ON u.id = gm.user_id
            WHERE gm.group_id = ?
            ORDER BY 
                CASE gm.role 
                    WHEN 'owner' THEN 1 
                    WHEN 'admin' THEN 2 
                    WHEN 'elder' THEN 3 
                    ELSE 4 
                END, gm.joined_at ASC
        ");
        $stmt->execute([$groupId]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->json([
            'group' => $group,
            'members' => $members,
            'my_role' => $myRole ?: 'none'
        ]);
    }

    // GET /api/groups/subgroups?group_id=X
    public function getSubgroups()
    {
        $this->requireAuth();
        $groupId = (int)($_GET['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);

        $stmt = $this->db->prepare("SELECT id, name, description FROM subgroups WHERE group_id = ? ORDER BY id ASC");
        $stmt->execute([$groupId]);
        $subgroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->json(['subgroups' => $subgroups]);
    }

    // POST /api/groups/avatar
    public function uploadGroupAvatar()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Only admins can change group avatar']);
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) $this->json(['error' => 'No file uploaded']);

        $dir = BASE_PATH . '/public/uploads/groups';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION)) ?: 'jpg';
        $filename = 'avatar_' . $groupId . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . '/' . $filename)) {
            $this->json(['error' => 'Failed to save file']);
        }
        $url = '/uploads/groups/' . $filename;
        $this->db->prepare("UPDATE groups SET avatar_url = ? WHERE id = ?")->execute([$url, $groupId]);
        $this->json(['success' => true, 'avatar_url' => $url]);
    }

    // POST /api/groups/cover
    public function uploadGroupCover()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Only admins can change group cover']);
        if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== UPLOAD_ERR_OK) $this->json(['error' => 'No file uploaded']);

        $dir = BASE_PATH . '/public/uploads/groups';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION)) ?: 'jpg';
        $filename = 'cover_' . $groupId . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES['cover']['tmp_name'], $dir . '/' . $filename)) {
            $this->json(['error' => 'Failed to save file']);
        }
        $url = '/uploads/groups/' . $filename;
        $this->db->prepare("UPDATE groups SET cover_url = ? WHERE id = ?")->execute([$url, $groupId]);
        $this->json(['success' => true, 'cover_url' => $url]);
    }

    // GET /api/groups/pending?group_id=X
    public function getPendingMessages()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_GET['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Admins only']);
        $stmt = $this->db->prepare("SELECT pm.*, u.username as sender_name FROM pending_messages pm JOIN users u ON u.id = pm.sender_id WHERE pm.group_id = ? ORDER BY pm.created_at ASC");
        $stmt->execute([$groupId]);
        $this->json(['pending' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    // POST /api/groups/pending/approve
    public function approvePendingMessage()
    {
        $myId = $this->requireAuth();
        $pendingId = (int)($_POST['pending_id'] ?? 0);
        if (!$pendingId) $this->json(['error' => 'Missing pending_id']);
        $stmt = $this->db->prepare("SELECT * FROM pending_messages WHERE id = ?");
        $stmt->execute([$pendingId]);
        $pm = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pm) $this->json(['error' => 'Not found']);
        $role = $this->getUserRole($pm['group_id'], $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Admins only']);
        $this->db->prepare("INSERT INTO messages (conversation_id, group_id, subgroup_id, sender_id, content, file_id) VALUES (0, ?, ?, ?, ?, ?)")
            ->execute([$pm['group_id'], $pm['subgroup_id'], $pm['sender_id'], $pm['content'], $pm['file_id']]);
        $this->db->prepare("DELETE FROM pending_messages WHERE id = ?")->execute([$pendingId]);
        $this->json(['success' => true]);
    }

    // POST /api/groups/pending/reject
    public function rejectPendingMessage()
    {
        $myId = $this->requireAuth();
        $pendingId = (int)($_POST['pending_id'] ?? 0);
        if (!$pendingId) $this->json(['error' => 'Missing pending_id']);
        $stmt = $this->db->prepare("SELECT group_id FROM pending_messages WHERE id = ?");
        $stmt->execute([$pendingId]);
        $pm = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pm) $this->json(['error' => 'Not found']);
        $role = $this->getUserRole($pm['group_id'], $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Admins only']);
        $this->db->prepare("DELETE FROM pending_messages WHERE id = ?")->execute([$pendingId]);
        $this->json(['success' => true]);
    }

    // POST /api/groups/join
    public function requestJoinGroup()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $stmt = $this->db->prepare("SELECT status, join_mode FROM groups WHERE id = ?");
        $stmt->execute([$groupId]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$group) $this->json(['error' => 'Group not found']);
        if ($group['status'] === 'closed') $this->json(['error' => 'This group is closed.']);
        if ($this->getUserRole($groupId, $myId)) $this->json(['error' => 'Already a member']);

        if ($group['join_mode'] === 'free') {
            $this->db->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')")->execute([$groupId, $myId]);
            $this->json(['success' => true, 'joined' => true]);
        } else {
            try {
                $this->db->prepare("INSERT INTO group_join_requests (group_id, user_id) VALUES (?, ?)")->execute([$groupId, $myId]);
                $this->json(['success' => true, 'pending' => true]);
            } catch (\Exception $e) { $this->json(['error' => 'Request already submitted']); }
        }
    }

    // GET /api/groups/join-requests?group_id=X
    public function getJoinRequests()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_GET['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Admins only']);
        $stmt = $this->db->prepare("SELECT jr.*, u.username FROM group_join_requests jr JOIN users u ON u.id = jr.user_id WHERE jr.group_id = ? ORDER BY jr.created_at ASC");
        $stmt->execute([$groupId]);
        $this->json(['requests' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    // POST /api/groups/join-requests/approve
    public function approveJoinRequest()
    {
        $myId = $this->requireAuth();
        $requestId = (int)($_POST['request_id'] ?? 0);
        if (!$requestId) $this->json(['error' => 'Missing request_id']);
        $stmt = $this->db->prepare("SELECT * FROM group_join_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $req = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$req) $this->json(['error' => 'Not found']);
        $role = $this->getUserRole($req['group_id'], $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Admins only']);
        $this->db->prepare("INSERT OR IGNORE INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')")->execute([$req['group_id'], $req['user_id']]);
        $this->db->prepare("DELETE FROM group_join_requests WHERE id = ?")->execute([$requestId]);
        $this->json(['success' => true]);
    }

    // POST /api/groups/join-requests/reject
    public function rejectJoinRequest()
    {
        $myId = $this->requireAuth();
        $requestId = (int)($_POST['request_id'] ?? 0);
        if (!$requestId) $this->json(['error' => 'Missing request_id']);
        $stmt = $this->db->prepare("SELECT group_id FROM group_join_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $req = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$req) $this->json(['error' => 'Not found']);
        $role = $this->getUserRole($req['group_id'], $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Admins only']);
        $this->db->prepare("DELETE FROM group_join_requests WHERE id = ?")->execute([$requestId]);
        $this->json(['success' => true]);
    }

    // POST /api/groups/invite-link
    public function generateInviteLink()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!in_array($role, ['owner', 'admin', 'elder'])) $this->json(['error' => 'You do not have permission to generate invite links']);

        $stmt = $this->db->prepare("SELECT status FROM groups WHERE id = ?");
        $stmt->execute([$groupId]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($group && $group['status'] === 'closed') $this->json(['error' => 'Group is closed. No new members can join.']);

        $token = bin2hex(random_bytes(16));
        $this->db->prepare("UPDATE groups SET invite_token = ? WHERE id = ?")->execute([$token, $groupId]);
        $this->json(['success' => true, 'invite_token' => $token]);
    }

    // POST /api/groups/join-via-link
    public function joinViaInvite()
    {
        $myId = $this->requireAuth();
        $token = trim($_POST['token'] ?? $_GET['token'] ?? '');
        if (!$token) $this->json(['error' => 'Missing invite token']);

        $stmt = $this->db->prepare("SELECT id, name, status, join_mode FROM groups WHERE invite_token = ?");
        $stmt->execute([$token]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$group) $this->json(['error' => 'Invalid or expired invite link']);
        if ($group['status'] === 'closed') $this->json(['error' => 'This group is closed and not accepting new members']);

        $existing = $this->getUserRole($group['id'], $myId);
        if ($existing) $this->json(['error' => 'You are already a member of this group']);

        if ($group['join_mode'] === 'approval') {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO group_join_requests (group_id, user_id) VALUES (?, ?)");
            $stmt->execute([$group['id'], $myId]);
            $this->json(['success' => true, 'pending' => true, 'message' => 'Join request submitted. Waiting for admin approval.']);
        } else {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')");
            $stmt->execute([$group['id'], $myId]);
            $this->json(['success' => true, 'message' => 'You have joined the group!', 'group_id' => $group['id']]);
        }
    }

    // POST /api/groups/avatar/remove
    public function removeGroupAvatar()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Only admins can remove group avatar']);
        $stmt = $this->db->prepare("SELECT avatar_url FROM groups WHERE id = ?");
        $stmt->execute([$groupId]);
        $g = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($g && $g['avatar_url']) { $path = BASE_PATH . '/public' . $g['avatar_url']; if (file_exists($path)) @unlink($path); }
        $this->db->prepare("UPDATE groups SET avatar_url = NULL WHERE id = ?")->execute([$groupId]);
        $this->json(['success' => true]);
    }

    // POST /api/groups/cover/remove
    public function removeGroupCover()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_POST['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!in_array($role, ['owner', 'admin'])) $this->json(['error' => 'Only admins can remove group cover']);
        $stmt = $this->db->prepare("SELECT cover_url FROM groups WHERE id = ?");
        $stmt->execute([$groupId]);
        $g = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($g && $g['cover_url']) { $path = BASE_PATH . '/public' . $g['cover_url']; if (file_exists($path)) @unlink($path); }
        $this->db->prepare("UPDATE groups SET cover_url = NULL WHERE id = ?")->execute([$groupId]);
        $this->json(['success' => true]);
    }

    // GET /api/groups/media?group_id=X
    public function getGroupMedia()
    {
        $myId = $this->requireAuth();
        $groupId = (int)($_GET['group_id'] ?? 0);
        if (!$groupId) $this->json(['error' => 'Missing group_id']);
        $role = $this->getUserRole($groupId, $myId);
        if (!$role) $this->json(['error' => 'Not a member']);

        // Files from messages
        $stmt = $this->db->prepare("
            SELECT m.id as message_id, m.content, m.created_at, m.sender_id, u.username as sender_name,
                   f.id as file_id, f.original_name, f.stored_name, f.mime_type, f.size
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            LEFT JOIN files f ON f.id = m.file_id
            WHERE m.group_id = ? AND (m.file_id IS NOT NULL OR m.content LIKE '%http%')
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$groupId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $photos = []; $videos = []; $documents = []; $links = [];
        foreach ($rows as $r) {
            if ($r['file_id']) {
                $item = ['file_id' => $r['file_id'], 'name' => $r['original_name'], 'stored' => $r['stored_name'], 'mime' => $r['mime_type'], 'size' => $r['size'], 'date' => $r['created_at'], 'sender' => $r['sender_name'], 'sender_id' => $r['sender_id']];
                if (strpos($r['mime_type'], 'image/') === 0) $photos[] = $item;
                elseif (strpos($r['mime_type'], 'video/') === 0) $videos[] = $item;
                else $documents[] = $item;
            }
            if ($r['content'] && preg_match_all('/https?:\/\/[^\s<>"\']+/', $r['content'], $m)) {
                foreach ($m[0] as $url) $links[] = ['url' => $url, 'date' => $r['created_at'], 'sender' => $r['sender_name'], 'sender_id' => $r['sender_id']];
            }
        }
        $this->json(['photos' => $photos, 'videos' => $videos, 'documents' => $documents, 'links' => $links]);
    }
}
