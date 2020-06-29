<body style='background: #F8F8FF;'>
    <div style='background: #FFF; padding: 15px; font-family: sans-serif;'>
        <p>
            <h1 style='color: #EE0000;'>Katrina alert: error in code execution</h1>
        </p>
        <hr>
        <p><strong>Type error:</strong> <?= $msg; ?><br>
            <hr>
        </p>
        <p><strong>Message:</strong> <?= $e->getMessage(); ?><br>
            <hr>
        </p>
        <p><strong>File:</strong> <?= $e->getFile(); ?><br>
            <hr>
        </p>
        <p><strong>Line:</strong> <?= $e->getLine(); ?><br>
            <hr>
        </p>
        <p><strong>Trace:</strong>
            <pre><?= $e->getTraceAsString(); ?></pre><br>
            <hr>
        </p>
    </div>
</body>