<!DOCTYPE html>
<html>
<head>
    <title>Handle Google Callback</title>
</head>
<body>
<script>
    function postToLogin() {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '/login';

        let csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        let apiToken = document.createElement('input');
        apiToken.type = 'hidden';
        apiToken.name = 'token';
        apiToken.value = '{{ $token }}';
        form.appendChild(apiToken);

        let provider = document.createElement('input');
        provider.type = 'hidden';
        provider.name = 'provider';
        provider.value = 'google';
        form.appendChild(provider);

        document.body.appendChild(form);
        form.submit();
    }

    window.onload = function() {
        postToLogin();
    };
</script>
</body>
</html>
