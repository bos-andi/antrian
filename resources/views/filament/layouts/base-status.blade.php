<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mesin Antrian Puskesmas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body style="font-family: 'poppins';" class="flex flex-col min-h-screen font-sans antialiased">
    <div class="flex-1">{{ $slot }}</div>
    <footer class="py-3 text-center text-sm text-gray-500">
        <a href="https://andidev.id" target="_blank" rel="noopener noreferrer" class="hover:text-blue-600 transition-colors">Powered by <span class="font-semibold">andidev</span></a>
    </footer>
</body>


</html>
