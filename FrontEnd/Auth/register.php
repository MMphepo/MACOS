<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Registration - MACOS</title>
    <link rel="stylesheet" href="../Public/complaint.css">
    <style>
        /* Additional styling for register page to match complaints system theme */
        body {
            background: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            max-width: 400px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .register-container h2 {
            color: #0a3d62;
            text-align: center;
            margin-bottom: 24px;
        }
        .register-form label {
            display: block;
            margin-bottom: 6px;
            color: #0a3d62;
            font-weight: 500;
        }
        .register-form input[type="text"],
        .register-form input[type="email"],
        .register-form input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 16px;
            border: 1px solid #b2bec3;
            border-radius: 5px;
            font-size: 1rem;
            background: #f9fafb;
        }
        .register-form button {
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
        .register-form button:hover {
            background: #145da0;
        }
        .register-message {
            margin-top: 16px;
            text-align: center;
            font-size: 1rem;
        }
        .register-message.success {
            color: #27ae60;
        }
        .register-message.error {
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Consumer Registration</h2>
        <form class="register-form" id="registerForm">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>
        <div class="register-message" id="registerMessage"></div>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const first_name = document.getElementById('first_name').value.trim();
            const last_name = document.getElementById('last_name').value.trim();
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            const role = "consumer"; // Hardcoded as required

            const messageDiv = document.getElementById('registerMessage');
            messageDiv.textContent = '';
            messageDiv.className = 'register-message';

            if (password !== confirm_password) {
                messageDiv.textContent = 'Passwords do not match.';
                messageDiv.classList.add('error');
                return;
            }

            const payload = {
                username,
                email,
                password,
                confirm_password,
                first_name,
                last_name,
                role
            };

            try {
                const response = await fetch('https://macos-u5hl.onrender.com/Auth/auth/register/', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (response.ok) {
                    messageDiv.textContent = 'Registration successful! Redirecting to login...';
                    messageDiv.classList.add('success');
                    document.getElementById('registerForm').reset();
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 1500);
                } else {
                    let errorMsg = data.detail || 'Registration failed.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).join(' ');
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
