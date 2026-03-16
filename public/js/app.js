// public/js/app.js
document.addEventListener('DOMContentLoaded', () => {
    // Add client-side validation to file uploads
    const formGroup = document.querySelector('.form-group input[type="file"]');

    if (formGroup) {
        const form = formGroup.closest('form');
        const submitBtn = form.querySelector('button[type="submit"]');

        formGroup.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                // 100MB limit matching server limit
                const maxSize = 100 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File is too larger. Maximum size is 100MB.');
                    e.target.value = ''; // clear input

                    const dropArea = document.querySelector('.border-dashed');
                    dropArea.style.borderColor = '#ef4444';
                    dropArea.style.background = 'rgba(239, 68, 68, 0.1)';
                    const displayNode = dropArea.querySelector('.file-name-display');
                    if (displayNode) displayNode.textContent = 'File too large! Must be < 100MB';
                    if (displayNode) displayNode.style.color = '#fca5a5';
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                    const dropArea = document.querySelector('.border-dashed');
                    dropArea.style.borderColor = '#8b5cf6';
                    dropArea.style.background = 'rgba(139, 92, 246, 0.1)';
                    const displayNode = dropArea.querySelector('.file-name-display');
                    if (displayNode) displayNode.textContent = 'Selected: ' + file.name;
                }
            }
        });

        form.addEventListener('submit', (e) => {
            if (formGroup.files.length > 0) {
                const originalText = submitBtn.textContent;
                submitBtn.innerHTML = '<svg style="animation: spin 1s linear infinite; margin-right: 8px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Uploading & Encrypting...';
                submitBtn.style.opacity = '0.7';
                submitBtn.style.pointerEvents = 'none';
            }
        });
    }

    // Add CSS for spinner inline
    const style = document.createElement('style');
    style.innerHTML = `@keyframes spin { 100% { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
});
