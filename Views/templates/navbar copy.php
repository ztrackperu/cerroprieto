<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
    <!--<meta name="viewport" content="width=device-width, user-scalable=no">-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo base_url; ?>Assets/css/navbar-1.css">
    <link rel="stylesheet" href="<?php echo base_url; ?>Assets/css/stylenav.css" />
    <link rel="stylesheet" href="<?php echo base_url; ?>Assets/css/main.css" />
    <link rel="stylesheet" href="<?php echo base_url; ?>Assets/css/select2.min.css"  />
    <link rel="stylesheet" href="<?php echo base_url; ?>Assets/css/fonts/remixicon.css" />
    <!-- BOOTSTRAP ICONS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
     <!-- DataTables CSS CDN -->
     <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- JQUERY UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    
    <!-- BANDERAS CSS 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
        SHOELACE COLOR PICKER 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.16.0/cdn/themes/light.css" /> -->
    
     <!-- Bootstrap TouchSpin CSS -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.css" rel="stylesheet">
    
    <title>ZTRACK | LIVE TELEMATIC</title>
    <style>
        #loading {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: 50% 50% #fff;
            opacity: 0.90;
            
        }
        /* centrando la img del gif*/
        .main-gif {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
<!--
<div id="loading">
    <img class="main-gif" src="<?php echo base_url;?>Assets/img/splash-screen.gif" alt="Loading..." />
</div>-->
<div class="wrapper">
    <aside id="sidebar">
        <div class="d-flex">
            <button class="logo-btn" type="button">
                <a href="<?php echo base_url?>AdminPage">
                    <svg class="icon" width="30" height="30">
                        <use xlink:href="sprite.svg#oso_icon"></use>
                    </svg>
                </a>
            </button>
            <div class="sidebar-logo">
                <a href="<?php echo base_url?>AdminPage">ZGROUP</a>
                <button type="button" class="btn-close float-end toggle-btn2" aria-label="Close" id="close-mobile" style="background-color:white;"></button>
            </div>
        </div>
        <ul class="sidebar-nav">
            <!--
            <li class="sidebar-item">
                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                    data-bs-target="#dashboard" aria-expanded="false" aria-controls="auth">
                    <i class="ri-home-3-line"></i>
                    <span>Reefer</span>
                </a>
            </li>
            -->
            <li class="sidebar-item">
                <a href="<?php echo base_url?>AdminPage" class="sidebar-link">
                    <i class="bi bi-snow"></i>
                    <span class="text-uppercase">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo base_url?>Control" class="sidebar-link">
                    <!--<i class="bi bi-wind"></i>-->
                    <i class="ri-settings-3-fill"></i>

                    <span class="text-uppercase">Manual Control</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo base_url?>Cooling" class="sidebar-link">
                    <i class="bi bi-wind"></i>
                    <span class="text-uppercase"> Cooling Mode</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo base_url?>Ripener" class="sidebar-link">
                    <i class="bi bi-clock-history"></i>
                    <span class="text-uppercase">Ripener Mode</span>
                </a>
            </li>
            <li class="sidebar-item" hidden>
                <a href="<?php echo base_url?>Receta" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                    data-bs-target="#recipe" aria-expanded="false" aria-controls="recipe">
                    <i class="ri-file-paper-2-line"></i>
                    <span>Recipe</span>
                </a>
                <ul id="recipe" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a href="<?php echo base_url?>Recipe/listar" class="sidebar-link">List</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo base_url?>Receta" class="sidebar-link">Add</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo base_url?>Recipe/editar" class="sidebar-link">Edit</a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo base_url?>Recipe/eliminar" class="sidebar-link">Delete</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo base_url?>Process" class="sidebar-link">
                    <i class="ri-settings-3-fill"></i>
                    <span class="text-uppercase">Automatic process</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="<?php echo base_url?>Graph" class="sidebar-link">
                    <i class="ri-line-chart-fill"></i>
                    <span class="text-uppercase">Graph</span>
                </a>
            </li>
           
            <li class="sidebar-item">
                <a href="<?php echo base_url?>Data" class="sidebar-link">
                    <i class="bi bi-table"></i>
                    <span class="text-uppercase">Data</span>
                </a>
            </li>
            <li class="sidebar-item" hidden>
                <a href="#" class="sidebar-link">
                    <i class="ri-question-line"></i>
                    <span class="text-uppercase">Commands</span>
                </a>
            </li>
            <li class="sidebar-item" hidden>
                <a href="#" class="sidebar-link">
                    <i class="ri-question-line"></i>
                    <span class="text-uppercase">Reports</span>
                </a>
            </li>
            <li class="sidebar-item" hidden>
                <a href="#" class="sidebar-link">
                    <i class="ri-question-line"></i>
                    <span class="text-uppercase">Email</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="ri-question-line"></i>
                    <span class="text-uppercase">Help</span>
                </a>
            </li>
        </ul>
    </aside>
          <!-- SEARCH BOX
                    <form class="">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Search...">
                            <span class="input-group-text bg-primary border-primary text-white">
                                Search
                            </span>
                        </div>
                    </form>
                    -->
    <div class="main">
        <nav class="navbar navbarLight navbar-expand-sm navbar-light d-flex justify-content-between ">
            <div class="d-flex justify-content-start">
                <button class="toggle-btn toggle-btnLight" type="button">
                    <i class="ri-align-justify"></i>
                </button>
                <form class="m-2" hidden>
                    <div class="input-group" id="buscador" style="width:100%; height:50%; top:10px;">
                        <input type="search" class="form-control search" placeholder="Search...">
                        <span class="input-group-text border-primary text-white" style="background-color:#6c7ef4">
                            Search
                        </span>
                    </div>
                </form>
            </div>
            <ul class="navbar-nav d-flex justify-content-end align-items-center navbarIcon">
                <li class="nav-item dropdown" hidden>
                    <a class="nav-link link-primary" id="notifyDropdown" role="button" data-bs-toggle="dropdown" aria-current="page">
                        <i class="ri-notification-line"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow bsb-zoomIn" aria-labelledby="conceptoDropdown">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom" style="width:350px;">
                                <a class="fw-normal text-muted">Notifications</a>
                                <a href="#!">Clear all</a>
                            </div>
                            <div class="d-flex flex-column justify-content-between p-3">
                                <a class="text-muted">Today</a>
                                <div class="alert border alert-dismissible fade show mt-3" role="alert">
                                    <strong>Holy guacamole!</strong> TEST.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <div class="alert border alert-dismissible fade show mt-3" role="alert">
                                    <strong>Holy guacamole!</strong> TEST.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex justify-content-center" href="#">View All</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link link-primary" href="" id="appsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">  <i class="ri-apps-line"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow bsb-zoomIn" aria-labelledby="conceptoDropdown" style="width:350px;">
                        <li class="row">
                        <div class="col-4">
                            <a class="dropdown-item" href="#!">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri-github-fill w-30 h-30"></i>
                                    <span>Github</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a class="dropdown-item" href="#!">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri-linkedin-fill w-30 h-30"></i>
                                    <span>Linkedin</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a class="dropdown-item" href="#!">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri-youtube-fill w-30 h-30"></i>
                                    <span>Youtube</span>
                                </div>
                            </a>
                        </div>
                        </li>
                        <li class="row">
                            <div class="col-4">
                                <a class="dropdown-item" href="#!">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-facebook-circle-fill w-30 h-30"></i>
                                        <span>Facebook</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a class="dropdown-item" href="#!">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-instagram-fill w-30 h-30"></i>
                                        <span>Instagram</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a class="dropdown-item" href="#!">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-twitter-fill w-30 h-30"></i>
                                        <span>Twitter</span>
                                    </div>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link link-primary" id="themeMode" role="button" data-bs-toggle="tooltip" data-bs-placement="left" title="Theme Mode">
                        <i class="ri-moon-line"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link link-primary" href="" id="fScreen" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-fullscreen-line"></i></a>
                </li>
                
                <li class="nav-item d-flex dropdown align-items-center p-1" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo base_url?>Assets/img/user1.jpg" class="img-radius rounded-circle img-profile" width="30" height="30" alt="...">
                    <a class="nav-link" href="">
                        <div class="text-secondary"><?php echo  $_SESSION['usuario_ztrack']?></div>
                        <div style="font-size:12px;" class="text-secondary"></div>
                    </a>
                </li> 
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow bsb-zoomIn" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="<?php echo base_url; ?>Usuarios/salir">Logout</a></li>
                </ul>
        </nav>
    
