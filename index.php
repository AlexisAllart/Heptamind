<?php 
session_start();
$default_tries=10;
$default_pawns=5;
$default_colors=8;

// RESET IF PLAYER CHEATS WITH $_GET
if ((isset($_SESSION['tries'])) && (isset($_SESSION['pawns'])) && (isset($_SESSION['colors'])) && (isset($_GET['tries'])) && (isset($_GET['pawns'])) && (isset($_GET['colors'])))
{
    if ($_SESSION['tries']!=$_GET['tries'] || $_SESSION['pawns']!=$_GET['pawns'] || $_SESSION['colors']!=$_GET['colors'])
    {
        header('Location: reset.php');
    }
}

// CHECK FOR CURRENT TRY #
if (isset($_SESSION['current_try']))
{
    if (isset($current_try))
    {
        if ($current_try<$_SESSION['current_try'])
        {
            $current_try=$_SESSION['current_try'];
        }
    }
    else
    {
        $current_try=$_SESSION['current_try'];
    }
}
else
{
    $current_try=0;
}

// SET UP SESSION VARS
if ((isset($_GET['tries'])) && (isset($_GET['pawns'])) && (isset($_GET['colors'])))
{
    $_SESSION['tries']=$_GET['tries'];
    $_SESSION['pawns']=$_GET['pawns'];
    $_SESSION['colors']=$_GET['colors'];
}

// INITIALIZE MASTERMIND
if ((isset($_SESSION['tries'])) && (isset($_SESSION['pawns'])) && (isset($_SESSION['colors'])) && (isset($_GET['tries'])) && (isset($_GET['pawns'])) && (isset($_GET['colors'])))
{
    if (isset($current_try))
    {
        if ($current_try==0)
        {
            for ($j=0;$j<$_SESSION['pawns'];$j++)
            {
                $_SESSION["master$j"]=mt_rand(1,$_SESSION['colors']);
                $_SESSION["success"]=false;
                $_SESSION["failure"]=false;
            }
        }
    }
}
?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="styles.css">
        <!-- TOGGLE DEBUG ON/OFF ON CLICK -->
        <script type="text/javascript">
        function toggle()  
        {
            var x = document.getElementById("debug");
            if (x.style.display === "none")
            {
                x.style.display = "block";
            }
            else
            {
                x.style.display = "none";
            }
        }
        </script>
        <!-- AUTOSCROLL DOWN ON LOAD -->
        <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {window.scrollTo(0,document.body.scrollHeight);});
        </script>
    </head>
    <body>
        <script src="" async defer></script>
        <form name="reset" action="logout.php">
            <input name="reset" type="submit" value="Logout" />
        </form>
        <br/>
        <form name="reset" action="reset.php">
            <input name="reset" type="submit" value="Reset" />
        </form>
        <br/>
        <button onclick="toggle()">Toggle Debug</button>
        <form name="options">
            <p>Number of tries :</p>
            <input name="tries" type="number" value="<?php if (isset($_GET['tries'])) {echo($_GET['tries']);} else {echo("10");}?>" />
            <p>Number of pawns :</p>
            <input name="pawns" type="number" value="<?php if (isset($_GET['pawns'])) {echo($_GET['pawns']);} else {echo("5");}?>" />
            <p>Number of colors :</p>
            <input name="colors" type="number" value="<?php if (isset($_GET['colors'])) {echo($_GET['colors']);} else {echo("8");}?>" />
            <?php if (!(isset($_SESSION['tries'])) && !(isset($_SESSION['pawns'])) && !(isset($_SESSION['colors']))) {echo('<input type="submit" value="Start game"/>');}?>
            <br/>
            <br/>
            <br/>
        </form>
        <?php
        if ((isset($_SESSION['tries'])) && (isset($_SESSION['pawns'])) && (isset($_SESSION['colors'])))
        {
            for ($i=0;$i<$_SESSION['tries'];$i++)
            {
                $incomplete_matches=0;
                $complete_matches=0;
                if ($current_try>=$i)
                {
                    echo("<form method='post' name=try$i>");
                    for ($j=0;$j<$_SESSION['pawns'];$j++)
                    {
                        echo("<select name=pawn$i$j>");
                        for ($k=0;$k<=$_SESSION['colors'];$k++)
                        {
                            echo("<option name=color$i$j$k>");
                            if (isset($_SESSION["player$i$j"]))
                            {
                                echo(($_SESSION["player$i$j"]));
                            }
                            else
                            {
                                if (isset($_POST["pawn$i$j"]))
                                {
                                    echo($_POST["pawn$i$j"]);
                                    $_SESSION["player$i$j"]=$_POST["pawn$i$j"];
                                }
                                else
                                {
                                    echo("$k");
                                }
                                // INITIALIZING PLAYER$i$j TO 0 IF PLAYER REFRESHES THE PAGE
                                if ($current_try!=$i && !isset($_SESSION["player$i$j"]))
                                {
                                    $_SESSION["player$i$j"]=0;
                                }
                                echo("</option>");
                            }
                        }
                        echo("</select>");
                    }
                }
                if ($current_try==$i && !$_SESSION['success'] && !$_SESSION['failure']) {echo("<input type='submit'>");}
                echo("</form>");
                // CHECKING MATCHING PAWNS - CHECK ALL LINES EXCEPT CURRENT TRY
                if ($current_try>0 && $i<$current_try)
                {
                    for ($a=0;$a<$_SESSION['pawns'];$a++)
                    {
                        $match_found[$a]=false;
                    }
                    // CHECKING COMPLETE MATCHES - WE HAVE A PERFECT MATCH IF MASTER$n = PLAYER$i$n, $i BEING THE TESTED LINE
                    for ($n=0;$n<$_SESSION['pawns'];$n++)
                    {
                        if ($_SESSION["master$n"]==$_SESSION["player$i$n"])
                        {
                            $match_found[$n]=true;
                            $complete_matches++;
                        }
                    }
                    // CHECKING INCOMPLETE MATCHES
                    for ($n=0;$n<$_SESSION['pawns'];$n++)
                    {
                        if ($_SESSION["master$n"]!=$_SESSION["player$i$n"])
                        {
                            for ($m=0;$m<$_SESSION['pawns'];$m++)
                            {
                                if ($match_found[$m]==false && $m!=$n && $_SESSION["master$m"]==$_SESSION["player$i$n"])
                                {
                                    $match_found[$m]=true;
                                    $incomplete_matches++;
                                    break;
                                }
                            }
                        }
                    }
                    if ($complete_matches==5)
                    {
                        echo "Success !";
                        $_SESSION['success']=true;
                        $_SESSION['failure']=false;
                    }
                    else
                    {
                        echo "Complete matches : ".$complete_matches;
                        echo "<br/>";
                        echo "Incomplete matches : ".$incomplete_matches;
                        echo "<br/>";
                        echo "<br/>";
                    }
                }
            }
        }
        if (isset($_SESSION['tries']))
        {
            if ($current_try<$_SESSION['tries'])
            {
                $_SESSION['current_try']=$current_try+1;
            }
            if ($current_try>=$_SESSION['tries'])
            {
                echo "Failure !";
                $_SESSION['failure']=true;
                $_SESSION['success']=false;
            }
        }
        ?>
        <!-- START DEBUG -->
        <div id="debug" style="display:none">
            <table>
            <?php
            echo "<br/>";
            echo "<br/>";
            echo "DEBUG_TABLE_BEGIN";
            echo "<br/>";
            echo "REAL_CURRENT_TRY=".$current_try;
            echo "<br/>";
            echo "MASTERCODE=";
            if (isset($_SESSION['pawns']))
            {
                for ($j=0;$j<$_SESSION['pawns'];$j++)
                {
                    echo $_SESSION["master$j"];
                }
            }
            else
            {
                echo "null";
            }
            echo "<br/>";
            foreach ($_SESSION as $key => $value)
            {
                echo "<tr>";
                echo "<td>";
                echo $key;
                echo "</td>";
                echo "<td>";
                echo $value;
                echo "</td>";
                echo "</tr>";
            }
            foreach ($_GET as $key => $value)
            {
                echo "<tr>";
                echo "<td>";
                echo $key;
                echo "</td>";
                echo "<td>";
                echo $value;
                echo "</td>";
                echo "</tr>";
            }
            echo "<br/>";
            foreach ($_POST as $key => $value)
            {
                echo "<tr>";
                echo "<td>";
                echo $key;
                echo "</td>";
                echo "<td>";
                echo $value;
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "DEBUG_TABLE_END";
            ?>
        </div>
        <!-- END DEBUG -->
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <form name="reset" action="reset.php">
            <input name="reset" type="submit" value="Reset" />
        </form>
    </body>
</html>