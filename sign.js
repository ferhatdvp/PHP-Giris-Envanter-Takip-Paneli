  const passwordInput = document.getElementById('password-input');
    const showPasswordBtn = document.getElementById('show-password-btn');


    if (passwordInput && showPasswordBtn) {
        showPasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                showPasswordBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye-off" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 3l18 18"></path><path d="M10.584 10.587a2 2 0 0 0 2.828 2.83"></path><path d="M9.363 5.365a12.186 12.186 0 0 1 2.637 -.365c3.27 0 6.6 2 9 6c-.687 1.407 -1.619 2.709 -2.771 3.794m-2.923 .026a12.186 12.186 0 0 1 -3.306 2.18c-3.27 0 -6.6 -2 -9 -6c1.11 -1.821 2.404 -3.149 3.827 -4.135"></path><path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path></svg>';
            } else {
                passwordInput.type = 'password';
                showPasswordBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path></svg>';
            }
        });
    }