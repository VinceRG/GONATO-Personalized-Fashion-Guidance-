(function () {
    let isEditing = false;
    const accountView = document.getElementById('accountView');
    const ordersView = document.getElementById('ordersView');
    const viewOrdersBtn = document.getElementById('viewOrdersBtn');
    const headerBackBtn = document.getElementById('headerBackBtn');
    const editProfileBtn = document.getElementById('editProfileBtn');
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, select');
    const overlay = document.querySelector('.overlay');
    const modalBody = document.querySelector('.modal-body');

    // Edit Profile Toggle
    editProfileBtn.addEventListener('click', () => {
        isEditing = !isEditing;

        if (isEditing) {
            inputs.forEach(input => input.disabled = false);
            editProfileBtn.innerHTML = '<i class="bi bi-check-lg"></i> Save Changes';
            editProfileBtn.classList.add('save');

            // Add Cancel button
            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'cancel-btn';
            cancelBtn.id = 'cancelBtn';
            cancelBtn.innerHTML = 'Cancel';
            editProfileBtn.parentElement.insertBefore(cancelBtn, editProfileBtn);

            cancelBtn.addEventListener('click', () => {
                inputs.forEach(input => input.disabled = true);
                editProfileBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Edit Profile';
                editProfileBtn.classList.remove('save');
                cancelBtn.remove();
                isEditing = false;
                form.reset();

                // Restore original values
                document.getElementById('firstName').value = 'Gabrielle';
                document.getElementById('lastName').value = 'Villamor';
                document.getElementById('email').value = 'useremail@gmail.com';
                document.getElementById('address').value = '123 Main St, Quezon City, Metro Manila';
                document.getElementById('phone').value = '+63 912 345 6789';
                document.getElementById('username').value = 'rielleir24';
            });
        } else {
            inputs.forEach(input => input.disabled = true);
            editProfileBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Edit Profile';
            editProfileBtn.classList.remove('save');
            document.getElementById('cancelBtn')?.remove();
            console.log('Saving profile:', Object.fromEntries(new FormData(form).entries()));
        }
    });

    // Switch to Orders View
    viewOrdersBtn.addEventListener('click', () => {
        accountView.classList.remove('active');
        ordersView.classList.add('active');

        // show header back button and prevent body scroll while modal active
        headerBackBtn.style.display = 'flex';
        document.documentElement.classList.add('no-scroll');
        document.body.classList.add('no-scroll');

        // reset modal body scroll to top for consistent UX
        if (modalBody) modalBody.scrollTop = 0;
    });

    // Back to Account View (now using header back button)
    headerBackBtn.addEventListener('click', () => {
        ordersView.classList.remove('active');
        accountView.classList.add('active');
        headerBackBtn.style.display = 'none';
        document.documentElement.classList.remove('no-scroll');
        document.body.classList.remove('no-scroll');
        if (modalBody) modalBody.scrollTop = 0;
    });

    // Close Modal
    const closeBtn = document.querySelector('.close-btn');

    closeBtn.addEventListener('click', () => {
        overlay.style.display = 'none';
        headerBackBtn.style.display = 'none';
        document.documentElement.classList.remove('no-scroll');
        document.body.classList.remove('no-scroll');
    });

    overlay.addEventListener('click', (e) => {
        if (e.target.classList.contains('overlay')) {
            overlay.style.display = 'none';
            headerBackBtn.style.display = 'none';
            document.documentElement.classList.remove('no-scroll');
            document.body.classList.remove('no-scroll');
        }
    });

    // Sub-tabs for Orders
    document.querySelectorAll('.sub-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sub-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.sub-tab-content').forEach(content => content.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(btn.dataset.subtab).classList.add('active');
        });
    });
})();