<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Share File</h2>
        <a href="/chat" class="btn btn-secondary">Back</a>
    </div>

    <p style="margin-bottom: 1rem; color: var(--text-color);">Filename: <strong><?= htmlspecialchars($file['original_name']) ?></strong></p>

    <?php if (isset($shareUrl)): ?>
        <div style="background: rgba(99, 102, 241, 0.1); border: 1px solid var(--primary-color); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
            <p style="margin-bottom: 0.5rem; color: var(--text-muted);">Share this link with others:</p>
            <input type="text" readonly value="<?= htmlspecialchars($shareUrl) ?>" class="form-control" style="text-align: center; margin-bottom: 1rem; font-weight: bold;">
            <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($shareUrl) ?>'); alert('Copied to clipboard!');" class="btn btn-primary">Copy Link</button>
        </div>
    <?php else: ?>
        <form action="/share/<?= $file['id'] ?>" method="POST">
            <div class="form-group">
                <label for="expires">Expiration Date (Optional)</label>
                <input type="datetime-local" id="expires" name="expires" class="form-control">
                <small style="color: var(--text-muted);">Leave blank for no expiration.</small>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Generate Link</button>
        </form>
    <?php endif; ?>
</div>
