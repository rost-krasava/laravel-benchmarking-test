<!DOCTYPE html>
<html>
<head>
    <title>Client Account Self Registration</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .content {
            text-align: center;
        }
        .content img {
            margin: 0 3em;
        }
        .hidden {
            display: none;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Добавлено */
        }
        .btn-primary {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .btn-primary:active {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
            text-align: center;
        }
        .alert-info {
            border: 1px solid #b01e44;
            line-height: 1.3;
            color: #b01e44;
            padding: 1em;
        }
    </style>
</head>
<body>
<div class="content">
    @auth
        <p><a href="{{ url('/home') }}">Get My Weather Data</a></p>
    @endauth
    @guest
        <div class="flex items-center justify-center mt-4" id="login-form">
            <a href="{{ url('auth/google') }}">
                <img height="50" src="https://icon-library.com/images/sign-in-with-google-icon/sign-in-with-google-icon-3.jpg">
            </a>
            <p>
                Don't Have An Account?
                <a class="nav-link" id="signup-tab" data-bs-toggle="tab" href="#tab-laravel">Sign Up Now</a>
            </p>
        </div>
    @endguest
    @auth
        <p><a href="{{ url('/logout') }}">Logout</a></p>
    @endauth

    <div class="flex items-center justify-center mt-4 hidden" id="signup-form">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Authenticate</button>
        </form>
        <p>
            Already Have A Google Account?
            <a class="nav-link" id="login-tab" data-bs-toggle="tab" href="#tab-google">Sign In</a>
        </p>
    </div>
    @if(session('error'))
        <div class="alert alert-info">
            {{ session('error') }}
        </div>
    @endif
</div>
<script>
    document.getElementById('signup-tab').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('login-form').classList.add('hidden');
        document.getElementById('signup-form').classList.remove('hidden');
    });

    document.getElementById('login-tab').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('signup-form').classList.add('hidden');
        document.getElementById('login-form').classList.remove('hidden');
    });
</script>
<script>
    // Проверяем, поддерживается ли Geolocation API в браузере
    if ("geolocation" in navigator) {
        // Получаем текущую геолокацию пользователя
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Получаем координаты пользователя
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                // Отправляем координаты на сервер
                // Можете использовать AJAX-запрос или любой другой метод отправки данных на сервер
                // Например, используя fetch API или jQuery.ajax()
                // В этом примере отправляем данные на маршрут '/save-location' с методом POST
                fetch('/save-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude,
                    }),
                })
                    .then(response => response.json()) // Преобразуем ответ в JSON
                    .then(data => {
                        // Обрабатываем данные из ответа
                        console.log('Status:', data.status);
                        console.log('Message:', data.message);
                        console.log('Location JSON:', data.json);

                        // Другие действия с данными, если необходимо
                    })
                    .catch(error => {
                        // Обрабатываем ошибки при отправке данных на сервер
                        console.error('Error saving location:', error);

                        // Если возможно, выведите дополнительные детали об ошибке
                        console.log('Error status:', error.status);
                        console.log('Error response:', error.response);
                    });
            },
            function(error) {
                // Обрабатываем ошибки получения геолокации
                console.error('Error getting location:', error);
            }
        );
    } else {
        // Если браузер не поддерживает Geolocation API
        console.error('Geolocation is not supported');
    }
</script>
</body>
</html>
