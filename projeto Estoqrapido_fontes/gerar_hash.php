<?php
$senha = '123';
$hash = password_hash($senha, PASSWORD_BCRYPT);
echo "Hash gerado para a senha '123':<br>" . $hash;
