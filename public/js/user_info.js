
let isEditing = false;

function toggleEdit() {
    isEditing = !isEditing;
    const inputs = document.querySelectorAll('#profileForm input');
    const editBtn = document.querySelector('.edit-btn');

    inputs.forEach(input => {
        input.disabled = !isEditing;
    });

    if (isEditing) {
        editBtn.innerHTML = '<i class="bi bi-check-lg"></i> Save Changes';
        editBtn.classList.add('save');
    } else {
        editBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Edit Profile';
        editBtn.classList.remove('save');
    }
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
    });
});

document.querySelectorAll('.sub-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sub-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.sub-tab-content').forEach(content => content.classList.remove('active'));

        btn.classList.add('active');
        document.getElementById(btn.dataset.subtab).classList.add('active');
    });
});

document.querySelector('.overlay').addEventListener('click', (e) => {
    if (e.target.classList.contains('overlay')) {
        window.history.back();
    }
});