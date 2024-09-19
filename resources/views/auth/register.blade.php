<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    @vite('resources/js/app.js') <!-- Incluye Vite para compilar los assets de Vue -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}"> <!-- Incluye los estilos de Laravel Mix si los estás usando -->
</head>
<body>
    <div id="app">
        <register></register> <!-- El nombre del componente Vue -->
    </div>

    <!-- Incluye el script de Vue compilado -->
    @vite('resources/js/app.js') <!-- Asegúrate de que el archivo esté correctamente vinculado -->
</body>
</html>
