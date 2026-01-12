<html>
    <head>
        <title>Moyenne</title>
    </head>
    <body>
        <?php
        require 'functions.php';
        $notes = [
            ['mat' => 'fonda', 'coef' => 1, 'note' => 14.5],
            ['mat' => 'bdd', 'coef' => 1, 'note' => 14.25],
            ['mat' => 'progra', 'coef' => 1, 'note' => 20],
            ['mat' => 'rezo', 'coef' => 1, 'note' => 17,5],
            ['mat' => 'cybersecu', 'coef' => 2, 'note' => 7.5],
            ['mat' => 'cybersecuSI', 'coef' => 2, 'note' => 12],
            ['mat' => 'CEJM', 'coef' => 3, 'note' => 6],
            ['mat' => 'cge', 'coef' => 2, 'note' => null],
            ['mat' => 'anglais', 'coef' => 1, 'note' => null]
        ];
        echo btsBlanc($notes, 2);
        ?>
    </body>
</html>