<!DOCTYPE html>
<html>

<head>
    <title>List of Files</title>
    <link rel="stylesheet" href="{{ asset('css/stylesFiles.css') }}">
    <!-- Font Awesome para iconos adicionales -->
    
</head>

<body>
    <h1>Bienvenid@, {{ $name }}</h1>
    <form action="" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="logout-button">Cerrar sesión</button>
    </form>
    <!-- Formulario de búsqueda -->
    <div class="search-container">
        <form action="{{ route('list.files') }}" method="get" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar documentos...">
            <input type="hidden" name="folderId" value="{{ $currentFolderId }}">
            <button type="submit"><i class="fas fa-search"></i> Buscar</button>
        </form>
    </div>

    <!-- Navegación de carpetas -->
    <div class="navigation">
        @if ($currentFolderId !== 'root' && $currentFolderId !== 'shared')
            <a href="{{ route('list.files', ['folderId' => 'root']) }}" class="nav-link">Volver al inicio</a>
            <a href="{{ route('list.files', ['folderId' => $parentFolderId, 'search' => request('search')]) }}" class="nav-link">Anterior</a>
        @elseif($currentFolderId === 'shared')
            <a href="{{ route('list.files', ['folderId' => 'root', 'search' => request('search')]) }}" class="nav-link">Back to root</a>
        @endif
    </div>

    <!-- Lista de archivos y carpetas -->
    <div class="file-list">
        <ul>
            @foreach ($files as $file)
                <li class="{{ isset($file['folder']) ? 'folder' : 'file' }}">
                    @if (isset($file['folder']))
                        @php
                            // Determina el icono de la carpeta
                            $icon = $currentFolderId === 'shared' ? 'folder_shared.png' : 'folder.png';
                        @endphp
                        <img src="{{ asset('icons/' . $icon) }}" alt="Folder Icon">
                        <a
                            href="{{ route('list.files', ['folderId' => $file['id'], 'search' => request('search')]) }}">{{ $file['name'] }}</a>
                    @elseif(isset($file['file']))
                        @php
                            // Determina el icono del archivo basado en su tipo
                            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $icon = 'file.png'; // Icono por defecto

                            if ($fileExtension === 'docx') {
                                $icon = 'doc.png';
                            } elseif ($fileExtension === 'pdf') {
                                $icon = 'pdf.png';
                            } elseif ($fileExtension === 'xlsx') {
                                $icon = 'xls.png';
                            } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $icon = 'photo.png';
                            } elseif ($fileExtension === 'ppt' || $fileExtension === 'pptx') {
                                $icon = 'ppt.png';
                            }
                        @endphp
                        <img src="{{ asset('icons/' . $icon) }}" alt="{{ $fileExtension }} Icon">
                        <span>{{ $file['name'] }}</span>
                        <a href="{{ route('file.download', ['file_id' => $file['id']]) }}"
                            class="download-button">Descargar</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</body>
    
</html>
