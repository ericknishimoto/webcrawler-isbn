<?php
@$conexao = mysqli_connect('localhost', 'root', 'root', 'webcrawler') or die(
    header("Location: erro?mysql=".mysqli_connect_errno()));
mysqli_set_charset($conexao,"utf8");
mysqli_query($conexao, "SET lc_time_names = 'pt_BR';"); 