<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<style>
body {
    background-color: #f2f2f2
}

.login {
    width: 100%;
    height: 100vh;
    align-items: center;
    justify-content: center;
    display: flex;
}
</style>
<img src="logo.png" alt="Logo" style="max-width: 400px; max-height: 400px; display: block; margin: 150px auto 0;">

<body>
    <div class="login">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 offset-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h3>Login</h3>
                        </div>
                        <div class="card-body">
                            <form action="login.php" method="POST">
                                <div>
                                    <div class="mb-3">
                                        <label for="usuario">Usuario</label>
                                        <input type="text" name="usuario" class="form-control">
                                    </div>
                                </div>
                                <div class="mb3">
                                    <label for="senha">Senha</label>
                                    <input type="password" name="senha" class="form-control">
                                    <br><br>
                                </div>
                                <div>
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">Enviar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>