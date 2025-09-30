<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MACOS</title>
    <link rel="stylesheet" href="../Public/complaint.css">
    <style>
        body {
            background: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .login-container h2 {
            color: #0a3d62;
            text-align: center;
            margin-bottom: 24px;
        }
        .login-form label {
            display: block;
            margin-bottom: 6px;
            color: #0a3d62;
            font-weight: 500;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 16px;
            border: 1px solid #b2bec3;
            border-radius: 5px;
            font-size: 1rem;
            background: #f9fafb;
        }
        .login-form button {
            width: 100%;
            background: #0a3d62;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-form button:hover {
            background: #145da0;
        }
        .login-message {
            margin-top: 16px;
            text-align: center;
            font-size: 1rem;
        }
        .login-message.success {
            color: #27ae60;
        }
        .login-message.error {
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form class="login-form" id="loginForm">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <div class="login-message" id="loginMessage"></div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('loginMessage');
            messageDiv.textContent = '';
            messageDiv.className = 'login-message';

            const payload = { username, password };

            try {
                const response = await fetch('https://macos-u5hl.onrender.com/Auth/auth/login/', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    messageDiv.textContent = data.message || 'Login successful!';
                    messageDiv.classList.add('success');
                    // Store token in localStorage for session management
                    if (data.data && data.data.token) {
                        localStorage.setItem('macos_token', data.data.token);
                        localStorage.setItem('macos_user', JSON.stringify(data.data.user));
                        // Redirect to complaint portal after login
                        window.location.href = '../Public/Complaint Portal.php';
                    }
                } else {
                    let errorMsg = data.message || 'Login failed.';
                    if (data.errors) {
                        errorMsg += ' ' + Object.values(data.errors).filter(Boolean).join(' ');
                    }
                    messageDiv.textContent = errorMsg;
                    messageDiv.classList.add('error');
                }
            } catch (error) {
                messageDiv.textContent = 'Network error. Please try again later.';
                messageDiv.classList.add('error');
            }
        });
    </script>
</body>
</html>
