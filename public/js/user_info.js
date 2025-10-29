document.addEventListener('DOMContentLoaded', () => {
    console.log("✅ Script running — tabs will now work!");

    let isEditing = false;

    function toggleEdit() {
        isEditing = !isEditing;
        const inputs = document.querySelectorAll('#profileForm input');
        const editBtn = document.querySelector('.edit-btn');

        inputs.forEach(input => input.disabled = !isEditing);

        if (isEditing) {
            editBtn.innerHTML = '<i class="bi bi-check-lg"></i> Save Changes';
            editBtn.classList.add('save');
        } else {
            editBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Edit Profile';
            editBtn.classList.remove('save');
        }
    }

    window.toggleEdit = toggleEdit;

    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        });
    });

    // Sub-tabs
    document.querySelectorAll('.sub-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sub-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.sub-tab-content').forEach(content => content.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(btn.dataset.subtab).classList.add('active');
        });
    });

    // Overlay close only when clicking outside modal
    const overlay = document.querySelector('.overlay');
    const modal = document.querySelector('.modal');

    if (overlay && modal) {
        overlay.addEventListener('click', (e) => {
            if (e.target.classList.contains('overlay')) {
                window.history.back();
            }
        });

        modal.addEventListener('click', (e) => e.stopPropagation());
    }
});
