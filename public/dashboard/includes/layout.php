<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>
    body {
        font-family: "Roboto";
    }
</style>
<body>
    <div id="app" class="md:flex antialiased">
        <?php require_once __DIR__.'/sidebar.php' ?>


          <main class="bg-gray-100 h-screen w-full overflow-y-auto">
                <?php require ($content ?? __DIR__.'/../pages/dashboard.php'); ?>
          </main>
    </div>
</body>
</html>

<script>
    document.querySelectorAll('.password-toggle').forEach(button=>{
        button.addEventListener('click', function(){
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');

            if(input.type === 'password')
            {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }else{
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>