<html>
<body>
    <form action="" method="GET">
        n1:<br>
        <input type="text" name="n1"><br>
        n2:<br>
        <input type="text" name="n2"><br>
        <input type="submit" value="invia">
    </form>
</body>

</html>

<?php
#$n=5;
#for($i=0;$i<$n;$i ++){
#    print("<b>ciao</b><br>");
#}

$n1=isset($_GET["n1"])?$_GET["n1"] : -1;
$n2=isset($_GET["n2"])?$_GET["n2"] : -1;
if($n1==-1 || $n2==-1){
    print("numeri non validi");
    
}else{
print("<table>");
for($i=0;$i<$n1;$i++){
    print("<tr>");
    for($j=0;$j<$n2;$j++){
        if($i==0){
            print("<th>$j</th>");
        }else{
            print("<td>$i,$j</td>");
        }
        
    }
    print("</tr>");
}
print("</table>");

}
?>