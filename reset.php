<?php 
session_start();

for ($i=0;$i<$_SESSION['tries'];$i++)
{
    for ($j=0;$j<$_SESSION['pawns'];$j++)
    {
        unset($_SESSION["player$i$j"]);
    }
}
for ($j=0;$j<$_SESSION['pawns'];$j++)
{
    unset($_SESSION["master$j"]);
}
unset($_SESSION['current_try']);
unset($_SESSION['tries']);
unset($_SESSION['pawns']);
unset($_SESSION['colors']);
unset($_SESSION['success']);
unset($_SESSION['failure']);

header('Location:index.php');
?>