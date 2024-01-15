
<style>

    .progressbar-step li:before {
        margin-bottom: 5% !important;
    }

    .tl-nme {
        font-size: 24px !important;
        margin-bottom:8% !important;
    }
    .tl-pname {
        font-size: 15px !important;
    }

    .custom-control-label::after {
        cursor: pointer;
    }
    .cur-point {
        cursor: pointer;
    }

    #nav-top i.fa.fa-angle-down {
        display: none;
    }
    .tl-hpage {
        position: relative;
        top: 20%;
    }

    .tl-nme {
        color: #107591;
        font-weight: 600;
        font-size: 15px;
        letter-spacing: 0.3px;
        margin-bottom: 3%;
    }
    .tl-pname {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2%;
    }
    .ed-nme { background: #fff5f5;
        padding: 1px 6px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 10px; }

    .page-container {
        width: 100%;
        height: 100%;
        min-height: 100vh;
        padding-left: 280px;
        -webkit-transition: padding-left 0.3s ease 0s;
        transition: padding-left 0.3s ease 0s;
    }

    .sbar_collapsed.page-container {
        padding-left: 0;
    }

    .card {
        border: none;
        border-radius: 4px;
        background-color: #fff;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }



    .card-body {
        padding: 25.6px;
        padding: 1.6rem;
    }

    .h-full {
        height: 100%;
    }

    .main-content {
        width: 100%;
        background: #F3F8FB;
    }

    .main-content-inner {
        padding: 0 0px 50px;
    }
    .container {
        max-width: 1440px;
    }
    /*------------------------- END Core Css -------------------*/


    /*-------------------- 2.1 Sidebar Menu -------------------*/

    .sidebar-menu {
        position: fixed;
        left: 0;
        top: 0;
        z-index: 99;
        height: 100vh;
        width: 280px;
        overflow: hidden;
        background: #303641;
        box-shadow: 2px 0 32px rgba(0, 0, 0, 0.05);
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .sbar_collapsed .sidebar-menu {
        left: -280px;
    }

    .main-menu {
        height: calc(100% - 100px);
        overflow: hidden;
        padding: 20px 10px 0 0;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .menu-inner {
        overflow-y: scroll;
        height: 100%;
    }

    .slimScrollBar {
        background: #2942fa!important;
        opacity: 0.1!important;
    }

    .sidebar-header {
        padding: 19px 32px 20px;
        background: #303641;
        border-bottom: 1px solid #343e50;
    }
    .sidebar-menu .logo{
        text-align: center;
    }
    .logo a {
        display: inline-block;
        max-width: 169px;
        padding: 10px;
    }

    .metismenu >li >a {
        padding-left: 32px!important;
    }
    .metismenu li a {
        position: relative;
        display: block;
        color: #8d97ad;
        font-size: 15px;
        text-transform: capitalize;
        padding: 15px 15px;
        letter-spacing: 0;
        font-weight: 400;
    }

    .metismenu li a i {
        color: #6a56a5;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .metismenu li a:after {
        position: absolute;
        content: '\f107';
        font-family: fontawesome;
        right: 15px;
        top: 12px;
        color: #8d97ad;
        font-size: 20px;
    }

    .metismenu li.active>a:after {
        content: '\f106';
    }

    .metismenu li a:only-child:after {
        content: '';
    }

    .metismenu li a span {
        margin-left: 10px;
    }

    .metismenu li.active>a,
    .metismenu li:hover>a {
        color: #fff;
    }

    .metismenu li li a {
        padding: 8px 20px;
    }

    .metismenu li ul {
        padding-left: 37px;
    }

    .metismenu >li:hover>a,
    .metismenu >li.active>a {
        color: #fff;
        background: #343942;
    }

    .metismenu li:hover>a,
    .metismenu li.active>a {
        color: #fff;
    }

    .metismenu li:hover>a i,
    .metismenu li.active>a i {
        color: #fff;
    }

    .metismenu li li a:after {
        top: 6px;
    }

    /*-------------------- END Sidebar Menu -------------------*/


    /*-------------------- 2.1.1 Horizontal Menu -------------------*/

    .body-bg {
        background: #fff;
    }

    .horizontal-main-wrapper {
        min-height: 100vh;
    }

    .horizontal-main-wrapper .container {
        max-width: 1440px;
    }

    .horizontal-main-wrapper .header-area,
    .horizontal-main-wrapper .mainheader-area {
        padding-left: 0;
        padding-right: 0;
    }

    .horizontal-main-wrapper .main-content-inner {
        padding: 0 0 20px;
    }

    .mainheader-area .notification-area {
        -webkit-transform: translateY(-11px);
        transform: translateY(-11px);
    }

    .mainheader-area {
        background: #C7222A;
        padding-left: 15px;
        position: relative;
        z-index: 99;
        margin-left: 10px;
        margin-right:10px;
    }

    .mainheader-area .logo a span {
        color: #843df9;
    }

    .horizontal-menu {
        position: relative;
    }

    .horizontal-menu ul li {
        display: inline-block;
        position: relative;
    }

    .horizontal-menu ul li a {
        display: block;
        font-size: 13px;
        padding: 10px 20px;
        color: #000000;
        text-transform: capitalize;
    }

    .horizontal-menu ul li:hover>a,
    .horizontal-menu ul li.active>a {
        color: #007BFF;
    }

    .horizontal-menu nav>ul>li:first-child>a {
        padding-left: 0;
    }

    .horizontal-menu ul li a i {
        margin-right: 5px;
    }

    .horizontal-menu .submenu {
        position: absolute;
        left: 0;
        top: 100%;
        z-index: 99;
        width: 200px;
        background: #fff;
        opacity: 0;
        visibility: hidden;
        border-top: 4px solid #007BFF;
        border-radius: 3px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
    }

    .horizontal-menu .submenu li {
        display: block;
    }

    .horizontal-menu ul li:hover>.submenu {
        opacity: 1;
        visibility: visible;
    }

    .horizontal-menu .mega-menu {
        position: inherit;
    }

    .horizontal-menu .mega-menu .submenu {
        width: 100%;
        max-width: 900px;
    }

    .horizontal-menu .mega-menu .submenu li {
        display: inline-block;
        width: calc(100% * (1/3) - 5px);
    }

    .header-bottom .search-box input {
        max-width: 350px;
        width: 100%;
    }

    /* mobile menu */

    .slicknav_menu {
        background: #8255f7;
        padding: 0;
        margin-top: 20px;
    }

    .slicknav_menu>a {
        display: block;
        width: 100%;
        padding: 15px;
        margin: 0;
        background: transparent;
    }

    .slicknav_menu .slicknav_icon {
        float: right;
    }

    .slicknav_menu .slicknav_icon-bar {
        box-shadow: none;
    }

    .slicknav_menu .slicknav_menutxt {
        font-weight: 500;
        text-shadow: none;
    }

    .slicknav_nav .slicknav_row,
    .slicknav_nav a {
        text-transform: capitalize;
    }

    .slicknav_nav .slicknav_row:hover {
        border-radius: 0;
        background: #8e66f7;
    }

    .slicknav_nav li i {
        width: 26px;
        display: inline-block;
    }

    .slicknav_nav .slicknav_item .slicknav_arrow {
        float: right;
    }

    /*-------------------- END Horizontal Menu -------------------*/


    /*-------------------- 2.2 Header Area -------------------*/

    .header-area {
        padding: 10px 0px;
        background: #fff;
        margin-left: 10px;
        margin-right: 10px;
        background: #da9089;
    }

    .nav-btn {
        margin-right: 30px;
        margin-top: 10px;
        cursor: pointer;
    }

    .nav-btn span {
        display: block;
        width: 22px;
        height: 2px;
        background: #b3aaaa;
        margin: 4px 0;
        border-radius: 15px;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
        box-shadow: 0 0 0 4px rgba(99, 96, 96, 0.03);
    }

    .sbar_collapsed .nav-btn span:nth-child(2) {
        opacity: 0;
    }

    .sbar_collapsed .nav-btn span:first-child {
        -webkit-transform: rotate(45deg)translate(5px, 5px);
        transform: rotate(45deg)translate(5px, 5px);
    }

    .sbar_collapsed .nav-btn span:last-child {
        -webkit-transform: rotate(-45deg)translate(3px, -3px);
        transform: rotate(-45deg)translate(3px, -3px);
    }

    .search-box form {
        position: relative;
    }

    .search-box input {
        width: 350px;
        border-radius: 33px;
        border: none;
        height: 40px;
        padding-left: 20px;
        padding-right: 40px;
        letter-spacing: 0;
        background: #f3eeff;
    }

    .search-box input::-webkit-input-placeholder {
        color: #b1a7a7;
    }

    .search-box input::-moz-placeholder {
        color: #b1a7a7;
    }

    .search-box input:-ms-input-placeholder {
        color: #b1a7a7;
    }

    .search-box form i {
        position: absolute;
        right: 21px;
        top: 14px;
        font-size: 14px;
        color: #b1a7a7;
    }


    /* notification-area */

    .notification-area {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        position: relative;
        z-index: 1;
    }

    .notification-area li {
        display: inline-block;
        margin-left: 20px;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .notification-area li.settings-btn.active i {
        color: #007BFF;
    }

    .notification-area li>i {
        font-size: 26px;
        color: #bdbcbc;
        vertical-align: middle;
        text-shadow: 0 0 8px rgba(0, 0, 0, 0.12);
        -webkit-transition: color 0.3s ease 0s;
        transition: color 0.3s ease 0s;
    }

    .notification-area li:hover>i,
    .dropdown.show>i {
        color: var(--primary-color);
    }

    .notification-area li i>span {
        position: absolute;
        right: -5px;
        top: -7px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: var(--primary-color);
        height: 20px;
        width: 20px;
        border-radius: 50%;
        text-align: center;
        line-height: 20px;
        padding-left: 2px;
    }

    .notify-box {
        width: 350px;
        border-radius: 10px;
        overflow: hidden;
        padding: 0;
        margin: 0;
    }

    .notify-title {
        background: var(--primary-color);
        display: block;
        padding: 18px 30px;
        color: #fff;
        font-size: 15px;
        letter-spacing: 0;
        overflow: hidden;
    }

    .notify-title a {
        float: right;
        display: inline-block;
        color: #ffee1d;
        font-size: 13px;
        text-decoration: underline;
    }

    .nofity-list {
        padding: 30px 0;
    }

    .nofity-list a {
        padding: 0 30px;
        display: block;
        margin-bottom: 20px;
        border-bottom: 1px solid #f5f2f2;
        padding-bottom: 15px;
    }

    .nofity-list a:last-child {
        margin-bottom: 0;
        border-bottom: none;
    }

    .notify-thumb {
        float: left;
        margin-right: 20px;
    }

    .notify-thumb i {
        height: 50px;
        width: 50px;
        line-height: 50px;
        display: block;
        border-radius: 50%;
        text-align: center;
        color: #fff;
        font-size: 23px;
    }

    .notify-text {
        overflow: hidden;
    }

    .notify-text p {
        font-size: 14px;
        color: #4e4e4e;
        line-height: 22px;
        margin-bottom: 4px;
    }

    .notify-text span {
        letter-spacing: 0;
        color: #272626;
        font-size: 11px;
        font-weight: 300;
    }

    /* notify envelope */

    .expanded .notification-area li#full-view-exit {
        display: inline-block;
    }

    .notification-area li#full-view-exit {
        display: none;
    }

    .expanded .notification-area li#full-view {
        display: none;
    }

    .notification-area li#full-view {
        display: inline-block;
    }

    .nt-enveloper-box .notify-text p {
        margin-bottom: 0;
    }

    .notify-text span.msg {
        display: block;
        font-size: 12px;
        color: #4e4e4e;
        line-height: 22px;
        margin-bottom: 4px;
        font-weight: 400;
    }

    .notify-thumb img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-top: 4px;
    }

    /*-------------------- END Header Area -------------------*/

    /*-------------------- 2.3 Page Title Area -------------------*/

    .page-title-area {
        padding: 0 30px;
        background: #fff;
        position: relative;
    }

    .page-title-area:before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 4px;
        height: 36px;
        background: var(--primary-color);
        -webkit-transform: translateY(-50%);
        transform: translateY(-50%);
    }

    .page-title {
        font-size: 24px;
        font-weight: 300;
        color: #313b3d;
        letter-spacing: 0;
        margin-right: 30px;
    }

    ul.breadcrumbs {
        margin-top: 4px;
    }

    .breadcrumbs li {
        display: inline-block;
    }

    .breadcrumbs li a,
    .breadcrumbs li span {
        display: block;
        font-size: 14px;
        font-weight: 400;
        color: #7801ff;
        letter-spacing: 0;
        margin-right: 16px;
        position: relative;
    }

    .breadcrumbs li a:before {
        content: '/';
        color: #768387;
        position: absolute;
        right: -13px;
        top: 0;
    }

    .breadcrumbs li span {
        margin-right: 0;
        color: #768387;
        text-transform: capitalize;
    }

    .user-profile {
        margin-right: -30px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        background: -webkit-linear-gradient(left, #8914fe 0%, #8063f5 100%);
        background: linear-gradient(to right, #8914fe 0%, #8063f5 100%);
        padding: 17px 38px;
        position: relative;
    }

    .user-profile img.avatar {
        height: 35px;
        width: 35px;
        border-radius: 50%;
        margin-right: 12px;
    }

    .user-name {
        font-size: 17px;
        font-weight: 500;
        color: #fff;
        letter-spacing: 0;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .user-name i {
        margin-left: 10px;
        font-size: 21px;
        vertical-align: middle;
        position: relative;
    }

    .notification-area .dropdown-toggle,
    .user-name.dropdown-toggle {
        position: relative;
    }

    .notification-area .dropdown-toggle:after,
    .user-name.dropdown-toggle:after {
        display: none;
    }

    .notification-area .dropdown-menu,
    .user-profile .dropdown-menu {
        background: #fff;
        border: none;
        -webkit-transform: none!important;
        transform: none!important;
        top: 130%!important;
        right: 30px!important;
        left: auto!important;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
        display: block!important;
        visibility: hidden;
        opacity: 0;
    }

    .notification-area .dropdown-menu.show,
    .user-profile .dropdown-menu.show {
        top: 100%!important;
    }

    .notification-area .dropdown-menu.show,
    .user-profile .dropdown-menu.show {
        top: 100%!important;
        visibility: visible;
        opacity: 1;
        box-shadow: 0 0 45px 0 rgba(131, 23, 254, 0.06);
    }

    .user-profile .dropdown-menu a {
        font-size: 14px;
        color: #8a8a8a;
        letter-spacing: 0;
        font-weight: 500;
        padding: 4px 120px;
        padding-left: 25px;
    }

    .user-profile .dropdown-menu a:hover {
        background: #f8f9fa;
        color: #2942fa;
    }

    /*-------------------- END Page Title Area -------------------*/


    /*-------------------- 2.4 Fact Area ------------------- */

    .single-report {
        background: #fff;
        overflow: hidden;
        position: relative;
    }

    .s-report-inner {
        padding-left: 85px;
    }

    .single-report .icon {
        font-size: 32px;
        color: #fff;
        background: var(--primary-color);
        height: 95px;
        width: 100px;
        text-align: right;
        padding-top: 40px;
        padding-right: 22px;
        border-radius: 50%;
        position: absolute;
        left: -39px;
        top: -30px;
    }

    .s-report-title {
        margin-bottom: 25px;
    }

    .header-title {
        font-family: 'Lato', sans-serif;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0;
        color: #333;
        text-transform: capitalize;
        margin-bottom: 17px;
    }

    .single-report p {
        font-size: 12px;
        font-weight: 700;
        color: #565656;
        background: #ececec;
        letter-spacing: 0;
        padding: 0 9px;
        height: 20px;
        line-height: 20px;
    }

    .single-report h2 {
        font-size: 26px;
        color: #565656;
        font-weight: 500;
        letter-spacing: 0;
    }

    .single-report span {
        font-size: 15px;
        font-weight: 600;
        color: #565656;
        letter-spacing: 0;
    }

    .highcharts-exporting-group {
        display: none;
    }

    /*-------------------- END Fact Area ------------------- */


    /*-------------------- 2.5 Overview ------------------- */

    .custome-select {
        font-size: 13px;
        color: #565656;
        font-weight: 500;
        letter-spacing: 0;
    }

    .border-0 {
        border: none;
    }

    #verview-shart {
        height: 400px;
    }

    #verview-shart-license-text,
    #coin_distribution-license-text {
        display: none;
    }

    #coin_distribution-wrapper {
        height: 400px!important;
        margin-top: -30px;
    }

    #coin_distribution-menu {
        display: none;
    }


    /*-------------------- END Overview ------------------- */


    /*-------------------- 2.6 Market value ------------------- */

    .market-status-table {
        overflow: hidden;
    }

    table.dbkit-table {
        width: 100%;
    }

    table.dbkit-table tr {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        height: 71px;
        border: 1px solid transparent;
        padding: 0 30px;
        margin: 20px 2px 6px;
        background: #f5f8f9;
    }

    table.dbkit-table tr th,
    table.dbkit-table tr td {
        border-top: none!important;
    }

    table.dbkit-table tr:hover {
        border-color: #e4e2f5;
    }

    table.dbkit-table tr td {
        font-size: 16px;
        font-weight: 400;
        letter-spacing: 0;
        color: #616161;
        min-width: 141px;
    }

    .mv-icon img {
        max-width: 29px;
    }

    .trends {
        text-align: center;
    }

    .stats-chart {
        text-align: center;
        max-width: 75px;
    }

    .stats-chart canvas {
        margin: auto;
    }

    .buy img,
    .sell img {
        margin-left: 10px;
    }

    /*-------------------- END Market value ------------------- */


    /*-------------------- 2.7 Live Crypto Price ------------------- */

    .cripto-live ul li {
        margin-bottom: 27px;
        font-size: 16px;
        font-weight: 500;
        color: #565656;
        letter-spacing: 0;
    }

    .cripto-live ul li:last-child {
        margin-bottom: 0;
    }

    .cripto-live ul li .icon {
        display: inline-block;
        height: 30px;
        width: 30px;
        margin-right: 10px;
        border-radius: 50%;
        font-size: 16px;
        font-weight: 700;
        color: #fff;
        background: #ffd615;
        text-align: center;
        line-height: 30px;
        text-transform: uppercase;
    }

    .cripto-live ul li .icon.l {
        background: #08bfc1;
    }

    .cripto-live ul li .icon.d {
        background: #4cff63;
    }

    .cripto-live ul li .icon.e {
        background: #8a7fe2;
    }

    .cripto-live ul li .icon.t {
        background: #95b36e;
    }

    .cripto-live ul li span {
        display: block;
        width: 50%;
        float: right;
    }

    .cripto-live ul li span i {
        color: #2fd444;
        margin-right: 10px;
    }

    .cripto-live ul li span i.fa-long-arrow-down {
        color: #ff0e0e;
    }

    /*-------------------- END Live Crypto Price ------------------- */


    /*-------------------- 2.8 Trading History ------------------- */

    .trd-history-tabs ul li a {
        font-size: 15px;
        font-weight: 500;
        color: #b0b0b0;
        letter-spacing: 0;
        margin: 0 15px;
        display: block;
        border-bottom: 2px solid transparent;
        padding-bottom: 7px;
    }

    .trd-history-tabs ul li a:hover,
    .trd-history-tabs ul li a.active {
        border-bottom: 2px solid #731ffd;
        padding-bottom: 7px;
        color: #565656;
    }

    /*-------------------- END Trading History ------------------- */


    /*-------------------- 2.9 Letest Post ------------------- */

    .single-post {
        margin-bottom: 34px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
    }

    .lts-thumb {
        -ms-flex-preferred-size: calc(40% - 10px);
        flex-basis: calc(40% - 10px);
    }

    .lts-content {
        -ms-flex-preferred-size: calc(60% - 10px);
        flex-basis: calc(60% - 10px);
    }

    .lts-content span {
        font-size: 16px;
        font-weight: 600;
        color: #565656;
        letter-spacing: 0;
    }

    .lts-content h2 a {
        display: block;
        font-size: 20px;
        font-weight: 700;
        color: #565656;
        letter-spacing: 0;
        margin-bottom: 16px;
    }

    .lts-content h2 a:hover {
        color: #6d65dc;
    }

    .lts-content p {
        font-size: 15px;
        font-weight: 400;
        color: #565656;
    }

    .input-form {
        position: relative;
    }

    .input-form input {
        height: 80px;
        width: 100%;
        padding-left: 50px;
        border: none;
        box-shadow: 0 0 41px rgba(67, 54, 251, 0.05);
    }

    .input-form span {
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 125px;
        background: var(--primary-color);
        text-align: center;
        line-height: 80px;
        font-weight: 600;
        color: #fff;
        letter-spacing: 0;
        font-size: 19px;
    }

    .exhcange-rate {
        padding: 38px;
        background: #f7fafb;
    }

    .exhcange-rate .exchange-devider {
        text-align: center;
        font-size: 30px;
        color: #686868;
        letter-spacing: 0;
        font-weight: 600;
        margin: 24px 0;
    }

    .exchange-btn button {
        width: 100%;
        margin-top: 20px;
        height: 79px;
        background: var(--primary-color);
        color: #fff;
        letter-spacing: 0;
        font-weight: 700;
        border: none;
        border-radius: 2px;
        font-size: 18px;
        outline: none;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .exchange-btn button:hover {
        background: #3c34ab;
    }

    .footer-area {
        text-align: center;
        padding: 10px 0 10px;
        background: #25093a;
    }

    .footer-area p {
        color: #fff;
        margin-bottom: 0;
        letter-spacing: 1px;
        font-size: 14px;
    }

    /*-------------------- END Letest Post ------------------- */


    /*-------------------- 3. Dashboard Two ------------------- */

    .sales-style-two .single-report {
        background: #fff;
        overflow: hidden;
        position: relative;
        padding: 10px 15px 10px;
    }

    #visitor_graph {
        height: 400px;
    }



    /* Todays Order List */

    .dbkit-table .heading-td td {
        color: #444;
        font-weight: 500;
    }

    .pagination_area ul li {
        display: inline-block;
    }

    .pagination_area ul li a {
        display: block;
        height: 45px;
        line-height: 44px;
        width: 45px;
        border: 2px solid #f1ebeb;
        border-radius: 50%;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        color: #4d4d4d;
        margin-left: 3px;
    }

    .pagination_area ul li a:hover {
        background: #8553fa;
        color: #fff;
    }

    .pagination_area ul li a i {
        font-size: 14px;
    }

    /* team member area */

    .team-search input {
        height: 40px;
        padding-left: 12px;
        border: 1px solid #e1e1e1;
        letter-spacing: 0;
        font-size: 13px;
        border-radius: 2px;
    }

    .team-search input::-webkit-input-placeholder {
        color: #e1e1e1;
    }

    .team-search input::-moz-placeholder {
        color: #e1e1e1;
    }

    .team-search input:-ms-input-placeholder {
        color: #e1e1e1;
    }

    .member-box .media-body {}

    .member-box .media-body p {
        font-size: 18px;
        font-weight: 500;
        color: #4d4d4d;
    }

    .member-box .media-body span {
        display: block;
        font-size: 15px;
        font-weight: 500;
        color: #4d4d4d;
        letter-spacing: 0;
    }

    .tm-social a {
        display: inline-block;
        margin-left: 15px;
        font-size: 22px;
        color: #555;
    }

    .s-member {
        padding: 16px 15px 17px;
        background: #f9f9f9;
        margin: 15px 0;
    }

    /*-------------------- END Dashboard Two ------------------- */


    /*-------------------- 4. Dashboard Three ------------------- */

    .seo-fact {
        border-radius: 3px;
    }

    .sbg1 {
        background: -webkit-linear-gradient(291deg, rgb(77, 77, 253) 0%, rgb(108, 143, 234) 100%);
        background: linear-gradient(159deg, rgb(77, 77, 253) 0%, rgb(108, 143, 234) 100%);
    }

    .sbg2 {
        background: -webkit-linear-gradient(291deg, rgb(5, 176, 133) 0%, rgb(27, 212, 166) 59%);
        background: linear-gradient(159deg, rgb(5, 176, 133) 0%, rgb(27, 212, 166) 59%);
    }

    .sbg3 {
        background: -webkit-linear-gradient(298deg, rgb(216, 88, 79) 0%, rgb(243, 140, 140) 100%);
        background: linear-gradient(152deg, rgb(216, 88, 79) 0%, rgb(243, 140, 140) 100%);
    }

    .sbg4 {
        background: -webkit-linear-gradient(59deg, rgb(254, 208, 63) 0%, rgb(230, 190, 63) 110%);
        background: linear-gradient(31deg, rgb(254, 208, 63) 0%, rgb(230, 190, 63) 110%);
    }

    .seofct-icon {
        color: #fff;
        font-size: 18px;
        font-weight: 500;
    }

    .seofct-icon i {
        font-size: 52px;
        margin-right: 20px;
        vertical-align: middle;
        -webkit-transform: translateY(-5px);
        transform: translateY(-5px);
        display: inline-block;
    }

    .seo-fact h2 {
        font-size: 27px;
        color: #fff;
        letter-spacing: 0;
    }

    .seo-fact #seolinechart3,
    .seo-fact #seolinechart4 {
        max-width: 200px;
    }

    /* socialads */

    .highcharts-credits {
        display: none;
    }

    /* user-statistics */

    #user-statistics {
        height: 350px;
    }

    /* salesanalytic */

    #salesanalytic {
        height: 500px;
    }

    /* timeline */

    .timeline-area {
        padding-left: 52px;
        padding-top: 6px;
    }

    .timeline-task {
        position: relative;
        margin-bottom: 35px;
    }

    .timeline-task:before {
        content: '';
        position: absolute;
        left: -35px;
        top: 0;
        height: calc(100% + 33px);
        width: 1px;
        background: #e6cdff;
    }

    .timeline-task:last-child:before {
        height: 100%;
    }

    .timeline-task:last-child {
        margin-bottom: 0;
    }

    .timeline-task .icon {
        position: absolute;
        left: -52px;
        top: -6px;
        height: 35px;
        width: 35px;
        line-height: 35px;
        border-radius: 50%;
        text-align: center;
        font-size: 12px;
        color: #fff;
    }

    .bg1 {
        background: #5c6df4;
    }

    .bg2 {
        background: #19D0A2;
    }

    .bg3 {
        background: #F7CB3F;
    }

    .tm-title {
        margin-bottom: 6px;
    }

    .tm-title h4 {
        font-size: 15px;
        letter-spacing: 0;
        color: #333;
        margin-bottom: 2px;
    }

    .tm-title span.time {
        color: #8c8a8a;
        font-size: 13px;
    }

    .tm-title span.time i {
        margin-right: 5px;
        color: #2c3cb7;
        font-size: 11px;
    }

    .timeline-task p {
        color: #666;
        font-size: 14px;
        line-height: 25px;
        margin-bottom: -10px;
    }

    .timeline-task:last-child p {
        margin-bottom: 0;
    }

    #seomap {
        height: 300px;
        width: 100%;
    }

    .map-marker {
        /* adjusting for the marker dimensions
      so that it is centered on coordinates */
        margin-left: -8px;
        margin-top: -8px;
    }

    .map-marker.map-clickable {
        cursor: pointer;
    }

    .pulse {
        width: 10px;
        height: 10px;
        border: 5px solid #f7f14c;
        border-radius: 30px;
        background-color: #716f42;
        z-index: 10;
        position: absolute;
    }

    .map-marker .dot {
        border: 10px solid #fff601;
        background: transparent;
        border-radius: 60px;
        height: 50px;
        width: 50px;
        -webkit-animation: pulse 3s ease-out;
        animation: pulse 3s ease-out;
        -webkit-animation-iteration-count: infinite;
        animation-iteration-count: infinite;
        position: absolute;
        top: -20px;
        left: -20px;
        z-index: 1;
        opacity: 0;
    }

    @-webkit-keyframes "pulse" {
        0% {
            -webkit-transform: scale(0);
            opacity: 0.0;
        }
        25% {
            -webkit-transform: scale(0);
            opacity: 0.1;
        }
        50% {
            -webkit-transform: scale(0.1);
            opacity: 0.3;
        }
        75% {
            -webkit-transform: scale(0.5);
            opacity: 0.5;
        }
        100% {
            -webkit-transform: scale(1);
            opacity: 0.0;
        }
    }

    .testimonial-carousel {
        margin: 60px 0 28px;
    }

    .tstu-img {
        width: 70px;
        width: 70px;
        border-radius: 50%;
        overflow: hidden;
        float: left;
        margin-right: 20px;
    }

    .tstu-content {
        overflow: hidden;
    }

    .tstu-name {
        font-size: 20px;
        color: #fff;
        letter-spacing: 0;
    }

    .profsn {
        display: block;
        font-size: 16px;
        color: #fff;
        letter-spacing: 0.02em;
        margin-bottom: 15px;
    }

    .tst-item p {
        color: #fff;
    }

    .testimonial-carousel .owl-dots {
        text-align: center;
        margin-top: 60px;
    }

    .testimonial-carousel .owl-dots>div {
        height: 11px;
        width: 11px;
        background: #fff;
        display: inline-block;
        border-radius: 50%;
        margin: 0 4px;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .testimonial-carousel .owl-dots .active {
        background: #f7cb3f;
    }

    /*-------------------- END Dashboard Three ------------------- */


    /*-------------------- 5. Bar Chart ------------------- */

    #ambarchart1,
    #ambarchart2,
    #ambarchart3,
    #ambarchart4,
    #ambarchart5,
    #ambarchart6 {
        height: 400px;
    }

    .amcharts-chart-div>a {
        display: none!important;
    }

    /*-------------------- END Bar Chart ------------------- */


    /*-------------------- 6. Line Chart ------------------- */

    #amlinechart1,
    #amlinechart2,
    #amlinechart3,
    #amlinechart4,
    #amlinechart5 {
        height: 400px;
    }



    /*-------------------- END Line Chart ------------------- */


    /*-------------------- 7. Pie Chart ------------------- */

    #ampiechart1,
    #ampiechart2,
    #ampiechart3,
    #highpiechart4,
    #highpiechart5,
    #highpiechart6 {
        height: 400px;
        width: 100%;
    }

    /*-------------------- END Pie Chart ------------------- */

    /*-------------------- 8. Accroding ------------------- */

    .according .card {
        margin-bottom: 20px;
    }

    .according .card:last-child {
        margin-bottom: 0;
    }

    .according .card-header {
        padding: 0;
        border: none;
        background: transparent;
        border-radius: 20px 20px 0px 0px;
    }

    .according .card-header a {
        display: block;
        background: #EFF3F6;
        padding: 16px 15px;
        border-radius: 3px;
        color: #444;
        letter-spacing: 0;
        font-size: 15px;
        font-weight: 500;
    }

    .according .card-body {
        padding: 10px;
        /*border: 1px solid #eff3f6;*/
        font-size: 14px;
        letter-spacing: 0;
        color: #444;
        line-height: 27px;
        font-weight: 400;
        border-top: 1px dashed #c7222a;
    }

    .according .card-header a {
        position: relative;
    }

    .accordion-s2 .card-header a.collapsed:before {
        content: "\f107";
    }

    .accordion-s2 .card-header a:before {
        content: "\f106";
        font-family: fontawesome;
        position: absolute;
        right: 13px;
        color: #444;
        font-size: 26px;
    }



    /* accordion-3 */

    .accordion-s3 .card-header a.collapsed:before {
        content: "\e61a";
    }

    .accordion-s3 .card-header a:before {
        content: "\e622";
        font-family: 'themify';
        position: absolute;
        right: 13px;
        color: #444;
        font-size: 15px;
    }

    .gradiant-bg .card-header a {
        background-image: -webkit-linear-gradient(top left, #8914fe, #8160f6);
        background-image: linear-gradient(to bottom right, #8914fe, #8160f6);
        color: #fff;
    }

    .gradiant-bg .card-header a:before {
        color: #fff;
    }

    /*-------------------- END Accroding ------------------- */


    /*-------------------- 9. Alert------------------- */

    .alert {
        letter-spacing: 0;
        font-size: 13px;
        border: none;
        padding: 10px 16px;
    }

    .alert strong,
    .alert-link {
        font-weight: 600;
    }

    .alert-items .alert-primary {
        color: #4796ea;
        background-color: #c8e1fb;
    }

    .alert-items .alert-primary .alert-link {
        color: #4796ea;
    }

    .alert-items .alert-success {
        color: #36b398;
        background-color: #cff1ea;
    }

    .alert-items .alert-success .alert-link {
        color: #36b398;
    }

    .alert-items .alert-danger {
        color: #f96776;
        background-color: #ffdde0;
    }

    .alert-items .alert-danger .alert-link {
        color: #f96776;
    }

    .alert-items .alert-warning {
        color: #d6a20c;
        background-color: #f9efd2;
    }

    .alert-items .alert-warning .alert-link {
        color: #d6a20c;
    }

    /* additional content */

    .alert-heading {
        margin-bottom: 7px;
    }

    .alert-dismiss .alert {
        padding: 13px 15px;
    }

    .alert-dismiss .alert-dismissible .close {
        top: 4px;
        outline: none;
        font-size: 13px;
    }

    /*-------------------- END Alert------------------- */


    /*-------------------- 10. Badge------------------- */

    .btn {
        padding: 11px 17px;
        font-size: 13px;
        letter-spacing: 0;
    }

    .btn-xl {
        padding: 19px 24px;
    }

    .btn-lg {
        padding: 15px 22px;
    }

    .btn-md {
        padding: 12px 19px;
    }

    .btn-sm {
        padding: 9px 14px;
    }

    .btn-xs {
        padding: 5px 10px;
    }

    .nav-pills .nav-link {
        padding: 16px 48px;
        padding: 1rem 3rem;
        line-height: 19px;
    }

    /*-------------------- END Badge------------------- */


    /*-------------------- 11. Button------------------- */

    .btn-flat {
        border-radius: 0;
    }

    .btn-rounded {
        border-radius: 50px;
    }

    /*-------------------- END Button ------------------- */

    /*-------------------- 12. Cards ------------------- */

    .title {
        font-size: 18px;
        color: #444;
        margin-bottom: 10px;
    }

    .card-bordered {
        border: 1px solid rgba(0, 0, 0, .125);
    }

    p.card-text {
        margin-bottom: 23px;
    }

    /*-------------------- END Cards ------------------- */


    /*-------------------- 13. Dropdown Button ------------------- */

    .drop-buttons .btn {
        margin-bottom: 1.3em;
    }

    .dropdown-item {
        font-size: 14px;
    }

    /*-------------------- END Dropdown Button ------------------- */


    /*-------------------- 14. List Group ------------------- */

    .list-group-item {
        color: #444;
        font-size: 13px;
    }

    .media-body {
        font-size: 13px;
        line-height: 27px;
    }


    /*-------------------- END List Group ------------------- */


    /*-------------------- 15. Modal ------------------- */

    .child-media {
        padding-left: 100px;
    }

    .modal-dialog.modal-xl {
        max-width: 100%;
    }

    /*-------------------- END Modal ------------------- */


    /*-------------------- 16. Pagination ------------------- */

    .pg-color-border li a {
        border-color: #007BFF;
    }

    .pagination li a {
        font-family: 'lato', sans-serif;
    }

    /*-------------------- END Pagination ------------------- */


    /*-------------------- 17. Form ------------------- */

    .form-control,
    .form-control:focus {
        outline: none;
        box-shadow: none;
    }

    .form-rounded {
        border-radius: 40px;
    }

    .form-control {
        font-size: 14px !important;
        border: 1px solid #e3e4e8 !important;
        padding: 10.72px 12.8px;
        padding: .80rem .8rem;
        font-weight: 600;
        letter-spacing: 0.4px;
        color: #000;
    }

    .form-control-sm,
    .input-group-sm>.form-control,
    .input-group-sm>.input-group-append>.btn,
    .input-group-sm>.input-group-append>.input-group-text,
    .input-group-sm>.input-group-prepend>.btn,
    .input-group-sm>.input-group-prepend>.input-group-text {
        padding: 4px 8px;
        padding: .25rem .5rem;
    }

    .form-control-lg,
    .input-group-lg>.form-control,
    .input-group-lg>.input-group-append>.btn,
    .input-group-lg>.input-group-append>.input-group-text,
    .input-group-lg>.input-group-prepend>.btn,
    .input-group-lg>.input-group-prepend>.input-group-text {
        padding: 13.6px 16px;
        padding: .85rem 1rem;
    }

    label {
        font-weight: 600;
        color: #000;
        display: inline-block;
        margin-bottom: 8px;
        margin-bottom: .5rem;
        font-size: 14px;
    }

    .font-14 {
        font-size: 14px;
    }

    .input-rounded {
        border-radius: 50px;
    }

    .custom-file-label,
    .custom-file-input,
    .custom-file {
        height: calc(2.25rem + 7px);
        padding: 10.8px 12px;
        padding: .675rem .75rem;
    }

    .custom-file-label:after {
        height: calc(calc(2.25rem + 7px) - 1px * 2);
        padding: 10.8px 12px;
        padding: .675rem .75rem;
    }

    .grid-col {
        padding: 10px 15px;
        background: #f3f8fb;
        margin-bottom: 30px;
        color: #666;
        border: 1px solid #e3e6e8;
    }

    .custom-control-label {
        margin-top: 0px;
    }

    /*-------------------- END Form ------------------- */


    /*-------------------- 18. Icons ------------------- */

    .fw-icons {}

    .fw-icons a {
        color: #444;
        margin: 9px 0;
        display: inline-block;
        font-family: 'lato', sans-serif;
    }

    .fw-icons a:hover {
        color: #007BFF;
    }

    .fw-icons a i {
        width: 30px;
        font-size: 14px;
    }



    /* icon-container */

    .icon-section {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }

    .icon-container {
        cursor: pointer;
        -ms-flex-preferred-size: calc(100% * (1/4));
        flex-basis: calc(100% * (1/4));
    }

    .icon-container [class^="ti-"] {
        width: 30px;
        font-size: 14px;
        display: inline-block;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .icon-container:hover [class^="ti-"] {
        -webkit-transform: scale(2);
        transform: scale(2);
        -webkit-transform-origin: left center;
        transform-origin: left center;
    }

    .icon-container span.icon-name {
        color: #444;
        margin: 9px 0;
        display: inline-block;
        font-family: 'lato', sans-serif;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .icon-container:hover span.icon-name {
        -webkit-transform: translateX(10px);
        transform: translateX(10px);
    }

    .icon-container:hover span {
        color: #007BFF;
    }

    /*-------------------- END Icons ------------------- */


    /*-------------------- 19. Table Basic ------------------- */

    .single-table .table {
        margin-bottom: 0;
    }

    table tr th {
        border-bottom: none;
    }

    table tr th,
    table tr td {
        border-top: 1px solid rgba(120, 130, 140, 0.13) !important;
    }

    table tr td [class^="ti-"] {
        cursor: pointer;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid rgba(120, 130, 140, 0.13) !important;
    }

    .status-p {
        color: #fff;
        padding: 0px 20px 1px;
        border-radius: 20px;
        display: inline-block;
        text-transform: capitalize;
        vertical-align: middle;
    }

    /*-------------------- END Table Basic ------------------- */


    /*-------------------- 20. Datatable ------------------- */

    div.dataTables_wrapper div.dataTables_length select {
        width: 76px;
        margin: 0 10px;
    }

    /* datatable-primary */

    .datatable-primary thead {
        background: #4336fb;
        color: #fff;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border: none;
        background: transparent;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0;
        border: none;
        margin-top: 20px;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid rgba(120, 130, 140, 0.13) !important;
    }

    table.dataTable thead th,
    table.dataTable thead td {
        border-bottom-color: transparent;
    }

    .datatable-primary .dataTables_paginate .page-item.active .page-link,
    .datatable-primary .dataTables_paginate .page-item .page-link:hover {
        background-color: #4336fb;
        border-color: #4336fb;
        color: #fff;
    }

    .datatable-primary .dataTables_paginate .page-link {
        color: #4336fb;
        border: 1px solid #4336fb;
    }

    .datatable-primary .dataTables_paginate .paginate_button.disabled,
    .datatable-primary .dataTables_paginate .paginate_button.disabled:hover,
    .datatable-primary .dataTables_paginate .paginate_button.disabled:active {
        color: #4336fb!important;
        border: none;
    }

    .datatable-primary .dataTables_paginate .page-item.disabled .page-link {
        color: #9f98f7;
        background-color: #f9f9f9;
        border-color: #c9c6f5;
    }



    /* datatable-dark */

    .datatable-dark thead {
        background: #444;
        color: #fff;
    }

    .datatable-dark .dataTables_paginate .page-item.active .page-link,
    .datatable-dark .dataTables_paginate .page-item .page-link:hover {
        background-color: #444;
        border-color: #444;
        color: #fff;
    }

    .datatable-dark .dataTables_paginate .page-link {
        color: #444;
        border: 1px solid #444;
    }

    .datatable-dark .dataTables_paginate .paginate_button.disabled,
    .datatable-dark .dataTables_paginate .paginate_button.disabled:hover,
    .datatable-dark .dataTables_paginate .paginate_button.disabled:active {
        color: #444!important;
        border: none;
    }

    .datatable-dark .dataTables_paginate .page-item.disabled .page-link {
        color: #999;
        background-color: #f9f9f9;
        border-color: #999;
    }

    /*-------------------- END Datatable ------------------- */


    /*-------------------- 21. Map Start ------------------- */

    #mapamchart1,
    #mapamchart2,
    #mapamchart3,
    #mapamchart4,
    #mapamchart5,
    #mapamchart6 {
        height: 400px;
    }

    #google_map {
        height: 600px;
    }

    /*-------------------- END Map Start ------------------- */

    /*-------------------- 22. Invoice ------------------- */

    .invoice-area {}

    .invoice-head {
        margin-bottom: 30px;
        border-bottom: 1px solid #efebeb;
        padding-bottom: 20px;
    }

    .invoice-head .iv-left span {
        color: #444;
    }

    .invoice-head span {
        font-size: 21px;
        font-weight: 700;
        color: #777;
    }

    .invoice-address h3 {
        font-size: 24px;
        text-transform: uppercase;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .invoice-address h5 {
        font-size: 17px;
        margin-bottom: 10px;
    }

    .invoice-address p {
        font-size: 15px;
        color: #555;
    }

    .invoice-date li {
        font-size: 15px;
        color: #555;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .invoice-table {}

    .invoice-table .table-bordered td,
    .invoice-table .table-bordered th {
        border: 1px solid rgba(120, 130, 140, 0.13) !important;
        border-left: none!important;
        border-right: none!important;
    }

    .invoice-table tr td {
        color: #666;
    }

    .invoice-table tfoot tr td {
        text-transform: uppercase;
        font-weight: 600;
        color: #444;
    }

    .invoice-buttons a {
        display: inline-block;
        font-size: 15px;
        color: #fff;
        background: #815ef6;
        padding: 12px 19px;
        border-radius: 3px;
        text-transform: capitalize;
        font-family: 'Lato', sans-serif;
        font-weight: 600;
        letter-spacing: 0.03em;
        margin-left: 6px;
    }

    .invoice-buttons a:hover {
        background: #574494;
    }

    /*-------------------- END Invoice ------------------- */


    /*-------------------- 23. Login ------------------- */

    .login-area {
        background: #F3F8FB;
    }

    .login-box {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        min-height: 100vh;
    }

    .login-box form {
        margin: auto;
        width: 450px;
        max-width: 100%;
        background: #fff;
        border-radius: 3px;
    }

    .login-form-head {
        text-align: center;
        background: #8655FC;
        padding: 50px;
    }

    .login-form-head h4 {
        letter-spacing: 0;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 7px;
        color: #fff;
    }

    .login-form-head p {
        color: #fff;
        font-size: 14px;
        line-height: 22px;
    }

    .login-form-body {
        padding: 50px;
    }

    .form-gp {
        margin-bottom: 25px;
        position: relative;
    }

    .form-gp label {
        position: absolute;
        left: 0;
        top: 0;
        color: #b3b2b2;
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .form-gp.focused label {
        top: -15px;
        color: #7e74ff;
    }

    .form-gp input {
        width: 100%;
        height: 30px;
        border: none;
        border-bottom: 1px solid #e6e6e6;
    }

    .form-gp input::-webkit-input-placeholder {
        color: #dad7d7;

    }

    .form-gp input::-moz-placeholder {
        color: #dad7d7;
    }

    .form-gp input:-ms-input-placeholder {
        color: #dad7d7;
    }

    .form-gp input:-moz-placeholder {
        color: #dad7d7;
    }

    .form-gp i {
        position: absolute;
        right: 5px;
        bottom: 15px;
        color: #7e74ff;
        font-size: 16px;
    }

    .rmber-area {
        font-size: 13px;
    }

    .submit-btn-area {
        text-align: center;
    }

    .submit-btn-area button {
        width: 100%;
        height: 50px;
        border: none;
        background: #fff;
        color: #585b5f;
        border-radius: 40px;
        text-transform: uppercase;
        letter-spacing: 0;
        font-weight: 600;
        font-size: 12px;
        box-shadow: 0 0 22px rgba(0, 0, 0, 0.07);
        -webkit-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .submit-btn-area button:hover {
        background: #2c71da;
        color: #ffffff;
    }

    .submit-btn-area button i {
        margin-left: 15px;
        -webkit-transition: margin-left 0.3s ease 0s;
        transition: margin-left 0.3s ease 0s;
    }

    .submit-btn-area button:hover i {
        margin-left: 20px;
    }

    .login-other a {
        display: block;
        width: 100%;
        max-width: 250px;
        height: 43px;
        line-height: 43px;
        border-radius: 40px;
        text-transform: capitalize;
        letter-spacing: 0;
        font-weight: 600;
        font-size: 12px;
        box-shadow: 0 0 22px rgba(0, 0, 0, 0.07);
    }

    .login-other a i {
        margin-left: 5px;
    }

    .login-other a.fb-login {
        background: #8655FC;
        color: #fff;
    }

    .login-other a.fb-login:hover {
        box-shadow: 0 5px 15px rgba(44, 113, 218, 0.38);
    }

    .login-other a.google-login {
        background: #fb5757;
        color: #fff;
    }

    .login-other a.google-login:hover {
        box-shadow: 0 5px 15px rgba(251, 87, 87, 0.38);
    }

    .form-footer a {
        margin-left: 5px;
    }

    /* login-s2 */

    .login-s2 {
        background: #fff;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .login-s2:before {
        content: '';
        position: absolute;
        height: 206%;
        width: 97%;
        background: #fcfcff;
        border-radius: 50%;
        left: -42%;
        z-index: -1;
        top: -47%;
        box-shadow: inset 0 0 51px rgba(0, 0, 0, 0.1);
    }

    .login-s2 .login-form-head,
    .login-s2 .login-box form,
    .login-s2 .login-box form .form-gp input {
        background: transparent;
    }

    .login-s2 .login-form-head h4,
    .login-s2 .login-form-head p {
        color: #444;
    }

    /* login-s3 */

    .login-bg {
        background: url(../images/bg/singin-bg.jpg) center/cover no-repeat;
        position: relative;
        z-index: 1;
    }

    .login-bg:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        z-index: -1;
        height: 100%;
        width: 100%;
        background: #272727;
        opacity: 0.7;
    }



    /* register 4 page */

    .login-box-s2 {
        min-height: 100vh;
        background: #f9f9f9;
        width: 100%;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
    }

    .login-box-s2 form {
        margin: auto;
        background: #fff;
        width: 100%;
        max-width: 500px;
    }

    /*-------------------- END Login ------------------- */


    /*-------------------- 24. Pricing ------------------- */

    .pricing-list {
        text-align: center;
    }

    .prc-head h4 {
        background: #805ff5;
        color: #fff;
        padding: 28px;
        letter-spacing: 0;
        font-family: 'lato', sans-serif;
        font-weight: 500;
    }

    .prc-list {
        padding: 30px;
    }

    .prc-list li a {
        display: block;
        font-size: 15px;
        letter-spacing: 0;
        margin: 23px 0;
        color: #6d6969;
    }

    .prc-list li.bold {
        font-weight: 600;
        margin-top: 20px;
    }

    .prc-list>a {
        display: inline-block;
        margin-top: 40px;
        background: #805ff5;
        color: #fff;
        padding: 11px 20px;
        border-radius: 40px;
    }

    .prc-list>a:hover {
        box-shadow: 0 3px 25px rgba(44, 113, 218, 0.38);
    }

    .dark-pricing .prc-head h4 {
        background: #3e3b3b;
        color: #fff;
    }

    .dark-pricing .prc-list>a {
        background: #3e3b3b;
    }

    .dark-pricing .prc-list>a:hover {
        box-shadow: 0 3px 25px rgba(27, 27, 27, 0.38);
    }

    /*-------------------- END Pricing ------------------- */


    /*-------------------- 25. 404 Page ------------------- */

    .error-area {
        min-height: 100vh;
        background: #F3F8FB;
    }

    .error-content {
        background: #fff;
        width: 100%;
        max-width: 600px;
        margin: auto;
        padding: 70px 30px;
    }

    .error-content h2 {
        font-size: 98px;
        font-weight: 800;
        color: #686cdc;
        margin-bottom: 28px;
        text-shadow: -3px -3px 0 #ffffff, 3px -3px 0 #ffffff, -3px 3px 0 #ffffff, 3px 3px 0 #ffffff, 4px 4px 0 #6569dc, 5px 5px 0 #6569dc, 6px 6px 0 #6569dc, 7px 7px 0 #6569dc;
        font-family: 'lato', sans-serif;
    }

    .error-content img {
        margin-bottom: 50px;
    }

    .error-content p {
        font-size: 17px;
        color: #787bd8;
        font-weight: 600;
    }

    .error-content a {
        display: inline-block;
        margin-top: 40px;
        background: #656aea;
        color: #fff;
        padding: 16px 26px;
        border-radius: 3px;
    }

    /*-------------------- END 404 Page ------------------- */

    /* ------  ABC ------- */

    .welcome-msg {
        color:#C7222A;
        padding-top: 40px;
        padding-bottom: 40px;
        text-align: center;
    }

    .welcome-msg b {
        color:#C7222A;
        font-size: 35px;
        line-height: 38px;
        letter-spacing: 0.2px;
    }
    .welcome-msg p {
        color:#DA9089;
        font-size: 16px;
        letter-spacing: 1px;
        padding-top: 10px;
    }
    .txt-abc {
        color:#C7222A;
        font-weight: 600;
    }
    .login-card {
        box-shadow: 0px 10px 40px 0px rgba(150,57,57,0.25);
        border-radius: 36px;
        padding-bottom: 14px;
        background-position: 87% -20% !important;
    }
    .head-login {
        line-height: 22px;
        font-size: 18px;
        letter-spacing: 1px;
        padding-left: 10px;
        padding-right: 10px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #3a0941;
    }
    .border-bot {
        border-bottom: 1px dashed #DA9089;
        position: absolute;
        max-width: 100%;
        left: 0;
        width:100%;
    }
    .btn-generate {
        border: 1px dashed #9b645e;
        border-radius: 50px;
        background: #ECC8C5;
        color: #C7222A;
        font-weight: 600;
        letter-spacing: 1.5px;
        font-size: 14px;
        margin-top: 37px;
    }
    .btn-verify {
        background: #C7222A;
        color: #fff;
        border: 1px dashed;
        font-size: 14px;
        border-radius: 50px;
        margin-top: 37px;
        letter-spacing: 2px;
        font-weight: 600;
    }
    .body-login {
        padding-top: 20px;
        padding-bottom: 20px;
    }
    .body-login label {
        letter-spacing: 1px;
        font-size: 15px;
    }
    .container-content {
        max-width: 1220px;
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }
    /* width */
    ::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .con-left {
        margin-left: 4px;
    }
    /* nav */
    .nav-listing {
        overflow: hidden;
    }

    .nav-listing a {
        float: left;
        display: block;
        color: #f2f2f2;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        font-size: 14px;
        letter-spacing: 0.4px;
    }

    .sidebar-icon {
        color: #fff;
        font-size: 23px;
    }
    /* end of nav */
    .brand-name {
        font-size: 13px;
        letter-spacing: 1px;
        font-weight: 600;
        color: #fff;
    }
    .sidebar-icon:hover {
        color: #fff;
    }
    .card-product {
        background: #faf0ef;
        border-radius: 100px;
    }

    .cont-product {
        max-width: 900px !important;
    }
    .pro-text {
        font-size: 18px;
        letter-spacing: 1.3px;
        font-weight: 600;
        color: #da9089;
    }
    .txt-brand {
        color:#C7222A;
    }
    .pro-img {
        position: absolute;
        margin-top: -40px;
        margin-left: 10px;
    }
    .border-pro {
        border-bottom: 1px dashed #DA9089;
        width: 100%;
        position: absolute;
        left: 0;
        max-width:100%;
    }
    .product-head {
        font-size: 25px;
        font-weight: 600;
        letter-spacing: 1px;
        padding-bottom: 10px;
        margin-top: -4px;
    }
    .product-head2 {
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 1px;
        padding-bottom: 10px;
        margin-top: -4px;
    }
    .pro-list {
        padding-top: 10px;
        font-size: 14px;
        letter-spacing: 0.4px;
        font-weight: 600;
        color: #444;
        padding-bottom: 10px;
    }

    .pro-list .fa-check {
        color: #92e137;
        padding-right: 10px;
        position: absolute;
        left: 15px;
        line-height: 31px;
    }
    .pro-list li {
        line-height: 30px;
        padding-left:10px;
    }
    .btn-select {
        background: #C7222A;
        color: #fff;
        border: 1px dashed;
        font-size: 14px;
        border-radius: 50px;
        letter-spacing: 2px;
        font-weight: 600;
    }
    .btn-buynow {
        background: #C7222A;
        color: #fff;
        border: 1px dashed;
        font-size: 14px;
        border-radius: 50px;
        letter-spacing: 2px;
        font-weight: 600;

        margin-top: 0px !important;
    }
    .img-pro-2 {
        border-radius: 100px;
        border: 1px dashed #65216e;
        padding: 10px 11px;
    }
    .btn-premium {
        border: 1px dashed #C7222A;
        border-radius: 50px;
        background: #faf0ef;
        color: #C7222A;
        font-weight: 600;
        letter-spacing: 1.5px;
        font-size: 12px;
        padding: 3px 8px;
        float: right;
        margin-top: 4px;
    }
    .header-pre {
        padding: 10px 16px;
        background: #f1f1f1;
        /* margin-left: 10px;*/
        /* margin-right: 10px;*/
        text-align: right;
        box-shadow: 0px 0px 16px 0px rgba(150,57,57,0.25);
        position: sticky !important;
    }
    .sticky-pre {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1;
    }
    .premium-top {
        font-size: 18px;
        letter-spacing: 1px;
        font-weight: 600;
        color: #C7222A;
        text-align: right;
        cursor: pointer;
    }
    .down-pre {
        font-weight: bold;
        content: "\f107";
        font-size: 22px;
        padding-left: 4px;
        padding-right: 4px;
    }
    .div-pro-range {
        text-align: center;
        border: 1px dashed #d27166;
        border-radius: 52px;
        padding: 7px 20px;
        cursor: pointer;
        background: #fff5f5;
    }

    .div-pro-range span {
        color:#000;
        font-size: 14px;
        letter-spacing: 1px;
    }
    .range-control {
        padding-left: 0.3rem !important;
    }
    .custom-control-label::after {
        width: 21px;
        height: 21px;
    }
    .custom-control-label::before {
        width: 21px;
        height: 21px;
    }
    .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
        background:  #c7222a !important;
    }
    .lbl-cover {
        font-size: 16px;
        font-weight: bold;
        color: #000;
        letter-spacing: 0.2px;
        padding-top: 20px;
        padding-bottom: 8px;
    }
    .tab-part {
        overflow: hidden;
    }

    /* Style the buttons inside the tab */
    .tab-part button {
        border-radius: 100px;
        padding: 7px 13px;
        color: #000;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight: 600;
        margin-right: 4px;
        border: none;
        background: none;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    .tab-part button:hover {
        background-color: #C7222A;
        color: #fff;
    }

    /* Create an active/current tablink class */
    .tab-part button.active {
        border-radius: 100px;
        border: 1px dashed #ffa5a9;
        padding: 7px 13px;
        background: #C7222A;
        color: #fff;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight: 600;
    }

    /* Style the tab content */
    .tabcontent-cover {
        display: none;
    }

    .tabcontent-cover {
        display: none;
    }
    .border-bot2 {
        border-bottom: 1px dashed #cccccc;
        width: 100%;
        position: absolute;
        left: 0;
        max-width:100%;
    }
    .head-tittle {
        font-size: 18px;
        font-weight: 600;
        color: #C7222A;
        margin-top: -5px;
        letter-spacing: 1px;
        margin-bottom:10px;
        font-family: 'Titillium', sans-serif;
    }
    .generate-footer {
        background: #C7222A;
        border-radius: 0px 0px 20px 20px;
        padding: 10px 10px;
        position: absolute;
        bottom:0;
        width: 100%;
        left: 0;
    }
    .buy-now-cta {
        margin-left: -11%;
        color: #fff !important;
        letter-spacing: 1px;
        font-weight: 600;
        font-size: 17px;
        line-height:37px;
        /* position: absolute; */
    }
    .back-btn {
        color: #C7222A !important;
        float: left;
        padding: 5px 10px !important;
        border-radius: 100px;
        letter-spacing: 1px;
        font-weight: 600;
        background: #fff5f5;
    }
    .form-group {
        margin-bottom: 1.1rem;
    }
    .fa-asterisk {
        font-size: 7px;
        margin-left: 2px;
        color: red;
    }
    .card-product-del .card {
        box-shadow: 0px 2px 22px 0px rgb(93 62 62 / 25%);
        border-radius: 25px;
        margin-top:10px;
    }
    .card-product-del .card-body {
        padding:1rem;
    }
    .del-ht {
        font-size: 15px;
        letter-spacing: 1px;
        font-weight: 600;
        color: #C7222A;
        margin-left: 4px;
    }
    .del-ht-img {
        border: 1px dashed #c47c7c;
        border-radius: 100px;
        padding: 4px;
        margin-left: -23px;
        margin-top: -30px;
        background: #fff4f4;
    }
    .fl-right {
        float: right;
        color: #C7222A;
        cursor: pointer;
    }
    .border-bot3 {
        border-bottom: 1px dashed #C7222A;
        width: 100%;
        margin-left: 0%;
        margin-top: 6px;
        margin-bottom: 5px;
    }
    .mem-del {
        border: 1px solid #da8089;
        padding: 5px 5px;
        border-radius: 3px;
        font-weight: 600;
        letter-spacing: 1px;
        font-size: 13px;
        margin-left: 6px;
        margin-bottom: 6px;
    }
    .sum-del {
        padding: 7px 10px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 1px;
        color: #C7222A;
        border-radius: 5px;
        border: 1px dashed #c7222a;
        background: #fff5f5;
        line-height: 19px;
    }
    .amt-del {
        font-size: 12px;
        color:#333;
        letter-spacing:0.3px;
    }
    .amt-del1 {
        float: right;
    }
    .mem-indi {
        padding-right: 6px;
    }
    .bor-indi {
        border-left: 1.2px dashed #da8089;
    }
    .amt-indi {
        padding-left: 9px;
        color: #d94756;
    }
    .card-gen {
        padding-bottom: 20px;
    }
    /*.tab-part button.active:after  {
    content: "";
    position: absolute;
    height: 10px;
    width: 10px;
    left: 50%;
    background: #6d00ff;
    transform: translate(-50%, 30%) rotate(45deg);
}*/
    .btn-submit {
        background: -webkit-linear-gradient(291deg, rgb(5, 176, 133) 0%, rgb(27, 212, 166) 59%) !important;
        background: linear-gradient(159deg, rgb(5, 176, 133) 0%, rgb(27, 212, 166) 59%) !important;
        border: 1px dashed rgb(19 97 77);
        color: #fff !important;
        font-weight: 600;
        border-radius: 100px;
        font-size: 14px;
        letter-spacing: 1.4px;
        padding: 8px 16px;
    }
    .ti-check {
        font-size: 13px;
        padding-left: 4px;
    }
    .modal-header {
        border-bottom: 1px dashed #c7222a;
        border-top-left-radius: 30px;
        border-top-right-radius: 30px;
        border-bottom-left-radius: 0px;
        border-bottom-right-radius: 0px;
    }
    .modal-footer {
        border-top: none;
        margin-top: -10px;
        background: #fff9f9;
        border-radius: 30px;
    }
    .modal-content {
        border-radius: 19px;
        border: none;
        background: #fff9f9;
    }
    .modal {
        background: #ffffffd1;
    }
    .ml-tittle {
        font-size: 15px;
        font-weight: bold;
        color: #000;
        letter-spacing: 0.4px;
        padding-top: 2px;
        padding-bottom: 10px;
    }
    .custom-radio .custom-control-input:checked~.custom-control-label::before {
        background: #C7222A;
    }
    .custom-radio .custom-control-label::before {
        border: 1px solid #C7222A;
        cursor: pointer;
    }
    .custom-checkbox .custom-control-label::before {
        border: 1px solid #C7222A;
        cursor: pointer;
    }
    .close {
        opacity: 1;
    }
    .accordion-s2 .card-header a:before {
        color: #c7222a;
        font-size: 20px;
        font-weight: 600;
    }
    .accordion-s2 .card-header a:before, .accordion-s3 .card-header a:before {
        right: 12px;
        top: 5px;
    }
    .according .card-header a {
        padding: 9px 10px;
        border-radius: 20px;
        background: transparent;
    }
    .according {
        border: 1px solid #c7222A;
        border-radius: 20px;
    }
    .according .card:last-child {
        margin-bottom: 0;
        border-radius: 20px;
    }
    .lbl-indi {
        margin-top: -2px;
    }
    .ml-indi {
        font-size: 13px;
        font-weight: bold;
        color: #000;
        letter-spacing: 1px;
        padding-top: 2px;
        padding-bottom: 10px;
    }
    .pro-del-indi {
        padding-right: 0px;
    }

    select.form-control:not([size]):not([multiple]) {
        height: calc(2.25rem + 14px) !important;
    }

    .mem-del-line {
        border-bottom: 1px dashed #da8089 !important;
    }
    .border-none {
        border: none !important;
    }
    .txt-member {
        font-weight: 600 !important;
        color: #000 !important;
        letter-spacing: 1px !important;
    }
    .body-mem {
        background: #FDF9F9;
        margin-top: 4px;
    }
    .back-btn-mem {
        background: #fff;
        border: 1px dashed #c7222a;
        border-radius: 20px;
        padding: 6px 16px;
        letter-spacing: 1px;
        font-weight: 600;
        color: #c7222a;
        font-size: 12px;
        margin-top: 4px;
        float: left;
    }
    .save-proceed-mem {
        border-radius: 7px;
        border: 1px dashed #ffa5a9;
        padding: 11px 25px;
        background: #C7222A;
        color: #fff;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight: 600;
        float: right;
    }
    .span-msg {
        font-size: 10px !important;
        line-height: 16px;
        color: #8c8c8c;
        letter-spacing: 0.2px;
        background: #f0f7ff;
        border-radius: 2px;
        padding: 4px 6px;
        margin-left: 0px !important;
        margin-top: 10px;
    }
    .pre-col {
        color: #da8089;
    }
    .thankyou_text {
        color: #c7222a;
        font-size: 21px;
        font-weight: 600;
        letter-spacing: 1px;
    }
    .text_booked {
        font-weight: 500;
        letter-spacing: 0.4px;
    }
    .thank-del {
        font-size:14px;
        font-weight: 500;
        line-height: 20px;
        text-align: center;
        letter-spacing: 0.3px;
        margin-bottom: 5px;
    }
    .pay_pol_sec{
        border-top: 1px dashed #E5CBD6;
        border-bottom: 1px dashed #E5CBD6;
        padding-top: 15px;
        padding-bottom: 15px;
        font-weight: 600;
        letter-spacing: 0.4px;
    }
    .btn-cta-down {
        width: 80%;
        color: #97144d;
        border: 1.5px solid #C7222A;
        padding-top: 13px;
        background: #E5CBD6;
    }
    .cta-down-img {
        padding-right: 5px;
        font-weight: 800;
        font-size: 12px;
        color:#C7222A;
    }
    .cta-down-txt{
        padding-right:10px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 1px;
    }
    .cta-down-hr {
        margin-left: -16px;
        border-bottom: 2px solid #fff;
        margin-bottom: -11px;
        margin-right: -16px;
        margin-top: 13px;
    }
    .txt-msg-body {
        font-weight: 600;
        letter-spacing: 1px;
        color: #c7222a;
        font-size: 16px;
    }
    .premium-top.dropdown-toggle::after {
        border: none;
    }
    .dropdown-menu {
        box-shadow: 0px 10px 40px 0px rgba(150,57,57,0.25);
        border: none;
        margin-top: 15px;
        left: -1% !important;
        min-width: 15rem;
        padding: 10px;
    }
    .cover-lbl {
        font-size: 14px;
        color: #c7222a;
        font-weight: 600;
        letter-spacing: 1px;
        padding-top: 10px;
        padding-left: 4px;
    }
    .cover-premium {
        font-size: 13px;
        color: #000;
        font-weight: 600;
        letter-spacing: 1px;
        margin-top: 10px;
    }
    .amt-drop {
        color: #c7222a;
        font-size: 14px;
        float: right;
    }
    .incl {
        font-size: 9px;
        color: #444;
    }
    .edit-review {
        text-align: center;
        margin-top: -6px;
        color: #c7222a;
    }
    .accordion-s3 .card-header a.collapsed:before  {
        color: #c7222a;
    }
    .accordion-s3 .card-header a:after {
        color: #c7222a;
    }
    .text_12, .text_11 {
        font-weight: 600;
        text-transform: capitalize !important;
        letter-spacing: 0.4px;
    }
    .text_13 {
        font-weight: 600;
        letter-spacing: 0.4px;
    }
    .mem-content {
        line-height: 20px;
    }
    .add-dot {
        text-align: left;
    }
    .mem-title {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 1px;
        color: #c7222a;
    }
    .col-form-label {
        letter-spacing: 0.5px;
    }
    .padding-bot {
        padding-top: 15px;
        padding-bottom: 15px;
    }
    /* step progress */
    /*.container-step{
    width: 100%;
    margin: 1px auto;
    z-index: 0;
    margin-left: -10px;
    margin-left: -125px;
    width: 850px;
  }*/

    .progressbar-step {
        counter-reset: step;
    }
    .progressbar-step li {
        list-style-type: none;
        width: 33%;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        text-transform: uppercase;
        color: #7d7d7d;
        cursor: pointer;
    }
    .progressbar-step li:before {
        width: 45px;
        height: 45px;
        content: counter(step);
        counter-increment: step;
        line-height: 44px;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: white;
        font-weight: 600;
        font-size: 19px !important;
        box-shadow: 0px 10px 32px 0px rgba(150,57,57,0.25);
        color: #c7222a;
    }
    .progressbar-step li:after {
        width: 100%;
        height: 1px;
        content: '';
        position: absolute;
        border: 1px dashed #cccccc;
        top: 22px;
        left: -50%;
        z-index: -1;
    }
    .progressbar-step li:first-child:after {
        content: none;
    }
    .progressbar-step li.active {
        color: green;
    }
    .progressbar-step li.active:before {
        border-color: #c7222a;
        background: #c7222a;
        color: #fff;
    }
    .progressbar-step li.active1:before {
        border-color: #c7222a;
        background: #c7222a;
        color: #fff;
        content: "\2713";
    }
    .progressbar-step li.active + li:after {
        border: 1px dashed #c7222a;
    }
    .card-z {
        z-index: 0;
    }
    .error{
        color: red;
        letter-spacing: 0.4px;
        font-size: 12px;
    }
    .progressbar-step li:before {
        margin-bottom: 10%;
    }
    .mar-bottom {
        margin-bottom: 2rem;
    }
    .box-gen {
        box-shadow: 0px 2px 22px 0px #ccc;
        border-radius:15px;
    }
    .custom-control {
        padding-left: 2rem;

    }
    .kn-btn{
        background: #EAECE2;
        color: #000;
        border: none;
        font-size: 14px;
        border-radius: 50px;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .faq-btn {
        background: #F6EFE1;
        color: #c7222a;
        border: none;
        font-size: 14px;
        font-size: 14px;
        border-radius: 50px;
        letter-spacing: 1px;
        font-weight: 600;
    }
    /* end */


    .back-btn {
        margin-top: 0px;
        padding: 11px 23px !important;
    }

    .save-proceed-mem {
        color: #fff !important;

    }
    .error {
        text-transform: capitalize !important;
    }
    .form-control {
        color: #000 !important;
    }
    .fa-pencil:before {
        content: "\f040";
        color: #C7222A!important;
    }
    .lbl1 {
        margin-top:0px!important;
    }
    .drop_prem {
        height: auto!important;
        overflow-y: auto!important;
        max-height: 430px;
    }

    .nom-vw {
        background: #fff;
        padding: 10px;
        letter-spacing: 0.5px;
        border-radius: 10px;
        margin-top: 10px;
        border: 1px dashed #ffe8e8;
    }
    .top-15 {
        top:15px;
    }
    .input-nom {
        width: 1.22rem;
        height: 1.2rem;
        color: #000;
        cursor: pointer;
    }
    .pd-lf-0 {
        padding-left:0px;
    }
    .pd-lf-0 p {
        font-weight:600;
        color:#333;
    }
    #nomineeModalContent ul {
        padding:10px;
    }
    .card-si {
        font-size: 12px;
        background: #ffefed;
        padding: 4px 25px;
        position: absolute;
        color: #333;
        font-weight: 600;
        margin-right: 4px;
        letter-spacing: 0.5px;
        border-radius: 10px 10px 0px 0px;
        margin-left: 20px;
        top: -39px;
        /*width: 86%;*/
        left: 5%;
        text-align: center;
        padding-top: 7px;
        padding-bottom: 14px;
        border: 1px dashed #ffd0d0;
        border-bottom: none;
    }
    .fam-btn {
        margin-top: 10px;
        background: #ffcfd1;
        color: #c7222a;
        padding: 5px 10px;
        font-weight: 600;
        margin-right: 4px;
        letter-spacing: 0.5px;
        border-radius: 10px;
    }
    .sum-txt {
        color: #c7222a;
    }

    .pd-lf {
        padding-left:0px;
    }
    .txt-rght {
        text-align: center;
        font-size: 20px;
        letter-spacing: 1px;
        line-height: 32px;
        text-transform: uppercase;
        font-weight: 600;
        font-family: 'Titillium';
        color: #d71921;
        padding-top: 8%;
    }

    .rght-btn {
        margin-top:24%;
        position:absolute;
        right:9%;
    }
    .back-btn-mem {
        background-color: #d71921 !important;
        border: 1px solid #d71921 !important;
        color: #fff !important;
    }
    .error {
        text-transform: initial;
    }
    .btn.focus, .btn:focus {
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgb(0 123 255 / 25%);
    }
    .otp-inp {
        border: 1px solid #FAF09F !important;
        border-radius: 17px !important;
    }
    .txt-otp {
        letter-spacing: 1px;
        line-height: 15px;
        margin-top: -3%;
        font-size: 12px;
    }
    .tm-otp {
        font-size: 12px;
        color: grey;
        font-weight: 600;
        letter-spacing: 1px;
    }
    .sweet-alert button:active {
        background-color: #c7222a !important;
    }

    .sweet-alert button:hover {
        background-color: #c7222a!important;
    }
    .resend-txt {
        font-size: 12px;
        text-align: center;
        letter-spacing: 1px;
    }
    .family_float_prod_img {
        border: 1px dashed #c7222a;
        border-radius: 12px;
        padding: 2px;
        float: left;
        margin-right: 8px;
    }
    /**checkbox*****/
    .btn-generate:hover {
        color: #107591 !important;
    }

    button.btn.btn-verify {
        color: #fff !important;
    }
    .btn-buynow {
        color: #fff !important;
    }
    .btn-buynow {
        letter-spacing: 1px !important;
        padding: 11px 13px !important;
    }
    .href-span {
        position: absolute;
        margin-top: 10px;
        margin-left: 4px;
    }

    /* Radio elements */

    .custom-radio .custom-control-input:checked~.custom-control-label::before {
        background-color: #007bff;
    }
    .custom-control-input:checked~.custom-control-label::before {
        color: #fff;
        background-color: #007bff;
    }
    .custom-radio .custom-control-label::before {
        border-radius: 50%;
    }
    .custom-control-label::before {
        position: absolute;
        top: 0.25rem;
        left: 0;
        display: block;
        width: 1rem;
        height: 1rem;
        pointer-events: none;
        content: "";
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-color: #dee2e6;
    }
    *, ::after, ::before {
        box-sizing: border-box;
    }
    .custom-radio .custom-control-input:checked~.custom-control-label::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='-2 -1.5 24 24' width='24' fill='%23fff'%3E%3Cpath d='M10 20.565c-5.523 0-10-4.477-10-10s4.477-10 10-10 10 4.477 10 10-4.477 10-10 10z'%3E%3C/path%3E%3C/svg%3E");
    }
    .custom-radio .custom-control-label::after {
        position: absolute;
        top: 0.23rem;
        left: 0.1px;
    }
    .custom-checkbox .custom-control-label::after {
        position: absolute;
        top: -0.3rem;
        left: -9px;
    }
    .custom-control-label::after {
        display: block;
        width: 1rem;
        height: 1rem;
        content: "";
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 50% 50%;
    }
    .custom-radio .custom-control-label {
        margin-bottom: 0;
    }
    .custom-control-label::after {
        cursor: pointer;
    }
    .custom-control {
        position: relative;
        display: block;
        min-height: 1.5rem;
        padding-left: 1.5rem;
    }
    .custom-checkbox .custom-control-label::before {
        border-radius: 4px;
    }
    .custom-checkbox  .custom-control-label::after {
        width: 35px !important;
        height: 36px !important;
    }
    .custom-checkbox .custom-control-input:checked~.custom-control-label::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='-5 -7 24 24' width='100' fill='%23fff'%3E%3Cpath d='M5.486 9.73a.997.997 0 0 1-.707-.292L.537 5.195A1 1 0 1 1 1.95 3.78l3.535 3.535L11.85.952a1 1 0 0 1 1.415 1.414L6.193 9.438a.997.997 0 0 1-.707.292z'%3E%3C/path%3E%3C/svg%3E");
    }
    input[type=checkbox], input[type=radio] {
        box-sizing: border-box;
        padding: 0;
    }
    .custom-control-input {
        position: absolute;
        z-index: -1;
        opacity: 0;
    }
    button, input {
        overflow: visible;
    }
    button, input, optgroup, select, textarea {
        margin: 0;
        font-family: inherit;
        font-size: inherit;
        line-height: inherit;
    }
    select.form-control {
        -webkit-appearance: menulist;
        appearance: menulist;
        -webkit-appearance:  menulist;
        -moz-appearance:menulist;
    }
    select {
        -webkit-appearance: menulist;
        appearance: menulist !important;
        -webkit-appearance:  menulist;
        -moz-appearance:menulist;
    }

    /* Toggel Accordion */

    .accordion-s3 .card-header.mem-del-line.open a.collapsed:before {
        content: "\e622";
    }
    .accordion-s3 .card-header.mem-del-line a.collapsed:after {
        content: "\e61a";
        display:none;
    }
    /* Modal */
    .close {
        background: none;
        border: none;
        color: #000;
        margin-top: -3%;
    }
    .close span {
        border: none;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
    }

    #editdetails-float .head-tittle {
        margin-bottom: 0px !important;
    }

    /* Dropdown premium */

    .drop_prem  {
        box-shadow: 0px 8px 16px 0px rgb(0 0 0 / 20%);
        z-index: 1;
    }
    .btn-close:focus {
        box-shadow: none !important;
    }

    .text-right {
        text-align: right;
    }

    /*****end checkbox****/
</style>
<input type="hidden" id="policyId" name="policyId" <?php $policy_id ?> />
<input type="hidden" id="planid" name="planid" value = "<?php  echo $post_data['plan_id'] ?>" />
<input type="hidden" id="cover" name="cover" <?php $cover ?> />
<div class="header-pre sticky-pre" id="myHeader-pre">
    <span class="premium-top dropdown-toggle"  data-toggle="dropdown" aria-expanded="true">Premium : <span class="total_premium"> <?php echo$quote_info['self_data']['premium'];?> </span> <i class="fa fa-angle-down down-pre"></i></span>
    <div class="dropdown-menu drop_prem plan_all_det" x-placement="bottom-start" style="position: absolute; transform: translate3d(15px, 44px, 0px); top: 0px; will-change: transform;overflow-y: scroll;height: 400px; right: 2%; left: unset !important; width: 120px;">
        <?php
        $arr_policy_id = [];
        foreach($quote_info['policy_details'] as $data_val)
        {
            array_push($arr_policy_id,$data_val['policy_id']);
            ?>
            <p class="text-cover"><img src="<?php echo $data_val['creditor_logo']?>" width="25"> <span class="cover-lbl"><?php echo $data_val['self']['sub_type_name'];?></span></p>
            <p class="cover-premium"> Premium <?php echo $data_val['self']['premium']?><span class="amt-drop"><span class="incl">(incl gst)</span></span></p>
            <?php

        }
        $policy_id = implode(',',$arr_policy_id);
        ?>


    </div>
</div>
<!-- header area end -->
<!-- page title area end -->
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
    <div class="container">
        <div class="row mt-2">
            <div class="col-lg-7 offset-lg-1 col-md-12 mt-2 mb-2 pd8">
                <div class="card login-card mb-3">
                    <div class="card-body">
                        <div class="generate-head head-tittle">

                            Generate Your Quote


                        </div>
                        <div class="border-bot2"></div>
                        <div class="generate-body">

                            <!-- <p class="lbl-cover">Select Cover</p> -->
                            <p class="lbl-cover"></p>
                            <div class="cover-tab">
                                <div class="tab-part">
                                    <button class="tablinks-cover active" onclick="covertab1(event, 'fam-float-id')" id="defaultactive"><?php echo $quote_info['get_suminsured_type']['suminsured_type'];?></button>
                                    <!-- <button class="tablinks-cover" onclick="covertab1(event, 'indi-id')">Individual</button> -->
                                    <!-- <button class="tablinks-cover">Individual</button> -->
                                </div>
                            </div>

                            <div id="fam-float-id" class="tabcontent-cover mb-3" style="display: block;">
                                <p class="lbl-cover mt-2">Select Product</p>
                                <div class="row family_floater">

                                    <div class="se-product col-md-4 col-6 mt-1 col-sm-4">
                                        <div class="mt-1">
                                            <div class="div-pro-range">
                                                <div class="custom-control custom-checkbox range-control check10">
                                                    <?php  $plan_exp = explode('-',$post_data['plan_name']);


                                                    ?>
                                                    <input type="checkbox" class="custom-control-input product_name" checked common-attr="family_floater" data-policyDetailId="" data-name="" name = "family_floater" id="customCheck" >
                                                    <label class="custom-control-label cur-point" for="customCheck"><img src="<?php echo $quote_info['policy_details'][0]['creditor_logo'];?>" width="30"> <span><?php echo $plan_exp[0];?></span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <p class="lbl-cover mt-2 mb-2">Selected Product</p>
                                <div class="family_floater_selected_prod">
                                    <div class="row mt-3">
                                        <div class="col-md-12 col-sm-12 col-12">
                                            <div class="card-product-del">
                                                <div class="card box-gen ">
                                                    <div class="card-body">
                                                        <div class="head-del">
                                                            <span><img class="del-ht-img" src="<?php echo $quote_info['policy_details'][0]['creditor_logo'];?>" width="40"></span>
                                                            <span class="del-ht"><?php echo $plan_exp[1];?></span>
                                                            <span class="ed-nme fl-right" data-toggle="modal" data-target="#editdetails-float"> <i class="fa fa-pencil"></i> Edit Details</span>
                                                            <div class="border-bot3"></div>
                                                            <div class="body-product-del mt-4 mb-3">
                                                                <?php
                                                                $arr_fam = [];
                                                                foreach($quote_info['member_ages'] as $family_con){

                                                                    array_push($arr_fam,$family_con['member_type']);




                                                                }
                                                                $sub_construct = '';
                                                                if (!empty($arr_fam)) {
                                                                    if (count($arr_fam) > 1) {

                                                                        $sub_construct = implode('+', $arr_fam);
                                                                    } else {
                                                                        $sub_construct = $arr_fam[0];
                                                                    }
                                                                }

                                                                ?>
                                                                <span class="mem-del fam_con_det fam_con_self"><?php echo $sub_construct;?> </span>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p class="sum-del mt-4">Plan <br><span class="amt-del sub_type_name_det"><?php echo$quote_info['self_data']['sub_type_name'];?></span></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="sum-del mt-4">Sum Insured<br>
                                                                            <?php foreach($quote_info['policy_details'] as $val_quote){
                                                                                $sum_club = $val_quote['self']['sub_type_name']." - ".$val_quote['self']['cover'];

                                                                                ?>
                                                                                <span class="amt-del cover_det coverNew"><?php echo $sum_club;?><br/></span>

                                                                                <?php

                                                                            }?>

                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="generate-footer text-center">
                            <a href="" class="btn back-btn">Back</a>
                            <a href="javascript:void(0)" class="" style="float: right;">
                                <button class="btn btn-buynow quoteBtn ClickBtn mt-2">Buy Now <i class="fas fa-long-arrow-alt-right rht-aw"></i></button>
                            </a>

                            <!--  <span class="buy-now-cta memProposerBtn">Buy Now <i class="fa fa-long-arrow-right"></i></span> -->
                        </div>
                        <!-- <div class="generate-footer text-center">
                    <a href="<?php echo base_url('comprehensive_product_abc'); ?>" class="btn back-btn">Back</a>
                   <a href="<?php echo base_url('member_proposer_detail'); ?>">  <span class="buy-now-cta">Buy Now <i class="fa fa-long-arrow-right"></i></span></a>
                    </div> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 mt-2 display-none-sm">
                <div class="card login-card" style="background:#fff; border-radius: 30px;padding-bottom: 0px;">
                    <div class="card-body" style="background: url(/assets/abc/images/family_new.png) no-repeat bottom;  background-size: 100%;
                  height: 617px; border-radius: 30px;">

                        <div class="tl-hpage">
                            <div class="tl-nme">Benefits of 3 plans Specially for You</div>
                            <p class="tl-pname"><i class="fa fa-check mr-2" style="color: #0cc50c;"></i>Group Activ Health Insurance</p>
                            <p class="tl-pname"><i class="fa fa-check mr-2" style="color: #0cc50c;"></i>Group Activ Critical Illness</p>
                            <p class="tl-pname"><i class="fa fa-check mr-2" style="color: #0cc50c;"></i>Group Activ Personal Accident</p>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$exist = 0;
$sub_type = [];
$policy_id_arr = [];
$sum_insure_combine = [];
$policy_with_premium = [];
//echo "<pre>";
//print_r($quote_info['policy_details']);exit;

foreach($quote_info['policy_details'] as $policy_val)
{

    if (($policy_val['is_combo'] == 1 && $policy_val['is_optional'] == 0) || ($policy_val['is_combo'] == 0 &&  $policy_val['is_optional'] == 0)) {
        // $exist = 1;
        $policy_with_premium[] = $policy_val['policy_id'] .'-'.$policy_val['self']['premium'];
        array_push($sub_type,$policy_val['policy_sub_type_name']);
        array_push($policy_id_arr,$policy_val['policy_id']);
        array_push($sum_insure_combine,$policy_val['sumInsured']);


    }
}

$policy_id_imp = implode(',',$policy_with_premium);

$data = [];
foreach($sum_insure_combine as $key => $sumDet)
{
    $data = array_merge($data,$sumDet);
}

$resl = [];
foreach($data as $vl)
{
    if(!in_array($vl['sum_insured'],$resl))
    {
        array_push($resl,$vl['sum_insured']);
    }

}


//print_r($quote_info['policy_details']);die;

?>
<!-- main content area end -->
<!-- Member Details Float -->
<div class="modal fade" id="editdetails-float" style="display: none; padding-right: 16px; padding-left: 0px;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content login-card">
            <div class="modal-header">
                <h5 class="modal-title head-tittle"><img class="family_float_prod_img" src="/assets/abc/images/combo_new.jpg" width="30"> <span class="family_flot_prod_name"><?php echo $post_data['plan_name']?></span></h5>
                <button type="button" class="close btn-close"  data-dismiss="modal" aria-label="Close"><span class="">×</span></button>
            </div>
            <div class="modal-body">
                <p class="ml-tittle">Select Family Construct</p>
                <div class="row family_flot_construct">

                    <?php
                    $i =1;
                    //                var_dump($arr_fam);
                    foreach($quote_info['get_family_construct_data']['adult'] as $fam_con){
                        $child_count_det = $quote_info['get_family_construct_data']['child'];
                        if($fam_con['is_adult'] == 'Y')
                        {
                            //$fam_con['member_type'] =   "Kid".$i;
                            //$i++;

                            ?><div class="col-md-3 col-6">
                            <?php
                            if(in_array($fam_con['member_type'],$arr_fam)){ ?>
                                <div class="custom-control custom-checkbox"><input type="checkbox" name="family_construct_arr[]" class="custom-control-input" data-kid = "<?php   echo $fam_con['is_adult'];?>"data-id="<?php echo $fam_con['id'];?>" id="customfrid<?php echo $fam_con['id'];?>" checked value = "<?php echo $fam_con['member_type'];?>"><label class="custom-control-label" for="customfrid<?php echo $fam_con['id'];?>"><span><?php echo $fam_con['member_type'];?></span></label></div>
                            <?php }else{ ?>
                                <div class="custom-control custom-checkbox"><input type="checkbox" name="family_construct_arr[]" class="custom-control-input" data-kid = "<?php   echo $fam_con['is_adult'];?>"data-id="<?php echo $fam_con['id'];?>" id="customfrid<?php echo $fam_con['id'];?>" value = "<?php echo $fam_con['member_type'];?>"><label class="custom-control-label" for="customfrid<?php echo $fam_con['id'];?>"><span><?php echo $fam_con['member_type'];?></span></label></div>
                            <?php }
                            ?>

                            </div>
                            <?php
                        }
                    }
                    for($i=1;$i<=$child_count_det;$i++)
                    {
                        $member_type =   "Kid".$i;
                        ?>
                        <div class="col-md-3 col-6">
                            <?php
                            if(in_array($member_type,$arr_fam)){ ?>
                                <div class="custom-control custom-checkbox"><input type="checkbox" checked name="family_construct_arr[]" class="custom-control-input" data-kid = "N" data-id="<?php echo $member_type;?>" id="customfrid_<?php echo $member_type;?>" value = "<?php echo $member_type;?>"><label class="custom-control-label" for="customfrid_<?php echo $member_type;?>"><span><?php echo $member_type;?></span></label></div>
                            <?php }else{ ?>
                                <div class="custom-control custom-checkbox"><input type="checkbox" name="family_construct_arr[]" class="custom-control-input" data-kid = "N" data-id="<?php echo $member_type;?>" id="customfrid_<?php echo $member_type;?>" value = "<?php echo $member_type;?>"><label class="custom-control-label" for="customfrid_<?php echo $member_type;?>"><span><?php echo $member_type;?></span></label></div>
                            <?php }
                            ?>

                        </div>
                        <?php

                    }



                    ?>

                </div>
                <p class="ml-tittle mt-3 age_title" style="">Enter age of the eldest member</p>
                <div class="row">
                    <div class="form-group col-md-6"> <input class="form-control" type="text" maxlength="3" name="max_age" id="max_age" autocomplete="off" value ="<?php echo $quote_info['max_age'];?>"> </div>
                </div>


                <?php
                $sub_name = implode('+',$sub_type);
                $sub_id = implode(',',$policy_id_arr);
                if($exist == 1) {
                    $k=0;
                    ?>
                    <div class="custom-control custom-checkbox"><div><input type="checkbox" checked readonly name = "chk_status[]" data-flag = 0 id= "checkbox_<?php echo $k;?>" disabled class="custom-control-input compare-checkbox1 disabled-checkbox" value="<?php echo $sub_id;?>"><label for="checkbox_<?php echo $k;?>" class=" custom-control-label" style="position: absolute;  left: 3px; top: 5px;">  </label> <p class="ml-tittle mt-3" ><?php echo $sub_name;?></p></div></div>


                    <p class="ml-tittle mt-3" > Select Sum Insured </p >
                    <div class="form-group dropdown_product_m_t_b_p">
                        <?php
                        //    var_dump($resl);
                        if(count($resl) <= 0 || $resl[0] == null){ ?>
                            <input type="number" id="sum_insured_<?php echo $k;?>" class="form-control" >
                        <?php   }else{ ?>
                            <select name="sum_insured" id="sum_insured_<?php echo $k;?>" class="form-control-age">
                                <option value="">- Select -</option>
                                <?php
                                foreach($resl as $valSum){
                                    if(count($resl) <=1)
                                    {
                                        $selected = 'selected';
                                    }
                                    else {
                                        $selected = '';
                                    }
                                    ?>
                                    <option value="<?php echo $valSum;?>" <?php echo $selected;?> ><?php echo $valSum;?></option>
                                    <?php
                                }
                                ?>


                            </select>
                        <?php  }
                        ?>


                    </div>

                    <?php
                }
                else {
                $k=1;
                foreach ($quote_info['policy_details'] as $policy_val) {
                $pol_id=$policy_val['policy_id'];
                if ($policy_val['is_combo'] == 0 && $policy_val['is_optional'] == 1) {

                $addtional_plan = 1;

                ?>
                <div class="custom-control custom-checkbox"><div>
                        <input type="checkbox"  name = "chk_status[]" checked  data-flag = "1" id= "checkbox_<?php echo $k;?>"  class="custom-control-input" value="<?php echo $policy_val['policy_id']; ?>>">
                        <label for="checkbox_<?php echo $k;?>" class=" custom-control-label" style="position: absolute; left: 3px; top: 5px;">  </label> <p class="ml-tittle mt-3" style="margin-left: 12px;"><?php echo $policy_val['policy_sub_type_name'];?></p></div></div>
                <p class="ml-tittle mt-3" > Select Sum Insured </p >
                <div class="form-group dropdown_product_m_t_b_p">
                    <?php

                    if($quote_info['basis_details'][$pol_id]==5){
                        ?>

                        <input type="number" value="<?php echo $val_r['sum_insured'];?>" max="500000" id="sum_insured_<?php echo $k;?>" class="form-control" >
                        <?php
                    }else{ ?>
                        <select name="sum_insured" id = "sum_insured_<?php echo $k;?>"class="form-control-age">
                            <option value="">- Select -</option>
                            <?php
                            foreach( $policy_val['sumInsured'] as $val_r)
                            {
                                if(count($policy_val['sumInsured']) <= 1)
                                {
                                    $selected = 'selected';
                                }
                                else {
                                    $selected = '';
                                }

                                ?>
                                <option value="<?php echo $val_r['sum_insured'];?>" <?php echo $selected;?>><?php echo $val_r['sum_insured'];?></option>
                                <?php
                                $k++;
                            } ?>
                        </select>
                    <?php } }
                    else  if ($policy_val['is_combo'] == 0 && $policy_val['is_optional'] == 0) {
                    $addtional_plan = 1;

                    ?>
                    <div class="custom-control custom-checkbox"><div>
                            <input type="checkbox"  name = "chk_status[]"  data-flag = "1" id= "checkbox_<?php echo $k;?>" checked disabled class="custom-control-input" value="<?php echo $policy_val['policy_id']; ?>>">
                            <label for="checkbox_<?php echo $k;?>" class=" custom-control-label" style="position: absolute; left: 3px; top: 5px;">  </label> <p class="ml-tittle mt-3" style="margin-left: 12px;"><?php echo $policy_val['policy_sub_type_name'];?></p></div></div>
                    <p class="ml-tittle mt-3" > Select Sum Insured </p >
                    <div class="form-group dropdown_product_m_t_b_p">
                        <?php if($quote_info['basis_details'][$pol_id]==5){
                            ?>

                            <input type="number" value="<?php echo $val_r['sum_insured'];?>" max="500000" id="sum_insured_<?php echo $k;?>" class="form-control" >
                            <?php
                        }else{ ?>
                            <select name="sum_insured" id = "sum_insured_<?php echo $k;?>"class="form-control-age">
                                <option value="">- Select -</option>
                                <?php
                                foreach( $policy_val['sumInsured'] as $val_r)
                                {
                                    if(count($policy_val['sumInsured']) <= 1)
                                    {
                                        $selected = 'selected';
                                    }
                                    else {
                                        $selected = '';
                                    }

                                    ?>
                                    <option value="<?php echo $val_r['sum_insured'];?>" <?php echo $selected;?>><?php echo $val_r['sum_insured'];?></option>
                                    <?php
                                    $k++;
                                } ?>
                            </select>
                        <?php } }else{ ?>
                        <div class="custom-control custom-checkbox"><div>
                                <input type="checkbox"  name = "chk_status[]"  data-flag = "1" id= "checkbox_<?php echo $k;?>" checked disabled class="custom-control-input" value="<?php echo $policy_val['policy_id']; ?>>">
                                <label for="checkbox_<?php echo $k;?>" class=" custom-control-label" style="position: absolute; left: 3px; top: 5px;">  </label> <p class="ml-tittle mt-3" style="margin-left: 12px;"><?php echo $policy_val['policy_sub_type_name'];?></p></div></div>
                        <p class="ml-tittle mt-3" > Select Sum Insured </p >
                        <div class="form-group dropdown_product_m_t_b_p">
                            <?php if($quote_info['basis_details'][$pol_id]==5){
                                ?>

                                <input type="number" value="<?php echo $val_r['sum_insured'];?>" max="500000" id="sum_insured_<?php echo $k;?>" class="form-control" >
                                <?php
                            }else{ ?>
                                <select name="sum_insured" id = "sum_insured_<?php echo $k;?>"class="form-control-age">
                                    <option value="">- Select -</option>
                                    <?php
                                    foreach( $policy_val['sumInsured'] as $val_r)
                                    {
                                        if(count($policy_val['sumInsured']) <= 1)
                                        {
                                            $selected = 'selected';
                                        }
                                        else {
                                            $selected = '';
                                        }

                                        ?>
                                        <option value="<?php echo $val_r['sum_insured'];?>" <?php echo $selected;?>><?php echo $val_r['sum_insured'];?></option>
                                        <?php
                                        $k++;
                                    } ?>
                                </select>
                            <?php  } }
                            ?>


                            <label class="over_border_txt_proposal_add">Sum Insured <span style="color:#FF0000">*</span></label>
                            <div class="help-block with-errors"></div>
                        </div>
                        <?php

                        }
                        }
                        ?>
                        <div class="modal-footer"> <input type="hidden" class="family_flot_policy_detail_id" name="family_flot_policy_detail_id" value="9" autocomplete="off"> <button type="button" class="btn btn-submit familyFloatBtn">Submit <i class="ti-check"></i></button> </div>
                    </div>
                </div>
            </div>
            <!-- end of Float -->

            <!-- Member Details Individual -->
            <div class="modal fade" id="editdetails-indi" style="display: none; padding-right: 16px;">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content login-card">
                        <div class="modal-header">
                            <h5 class="modal-title head-tittle"><img src="/public/assets/abc/images/supertopup.png" width="30"> <span>Heart Secure</span></h5>
                            <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="">×</span></button>
                        </div>
                        <div class="modal-body">
                            <p class="ml-tittle">Select Family Contsruct</p>
                            <div class="row">
                                <div class="col-md-6 col-6 mt-2">
                                    <div id="accordion2" class="according accordion-s2">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#accordion21">  <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck33">
                                                        <label class="custom-control-label" for="customCheck33"><span>Self</span></label>
                                                    </div></a>
                                            </div>
                                            <div id="accordion21" class="collapse" data-parent="#accordion2">
                                                <div class="card-body">
                                                    <p class="ml-indi">Select Sum Insured</p>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio21">
                                                                <label class="custom-control-label lbl-indi" for="customRadio21"><span>5 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio22">
                                                                <label class="custom-control-label lbl-indi" for="customRadio22"><span>10 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio23">
                                                                <label class="custom-control-label lbl-indi" for="customRadio23"><span>15 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio24">
                                                                <label class="custom-control-label lbl-indi" for="customRadio24"><span>20 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6 mt-2">
                                    <div id="accordion2" class="according accordion-s2">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#accordion22">  <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck33">
                                                        <label class="custom-control-label" for="customCheck33"><span>Spouse</span></label>
                                                    </div></a>
                                            </div>
                                            <div id="accordion22" class="collapse" data-parent="#accordion2">
                                                <div class="card-body">
                                                    <p class="ml-indi">Select Sum Insured</p>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio71">
                                                                <label class="custom-control-label lbl-indi" for="customRadio71"><span>5 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio72">
                                                                <label class="custom-control-label lbl-indi" for="customRadio72"><span>10 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio73">
                                                                <label class="custom-control-label lbl-indi" for="customRadio73"><span>15 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio74">
                                                                <label class="custom-control-label lbl-indi" for="customRadio74"><span>20 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6 mt-2">
                                    <div id="accordion2" class="according accordion-s2">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#accordion23">  <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck33">
                                                        <label class="custom-control-label" for="customCheck33"><span>Kid 1</span></label>
                                                    </div></a>
                                            </div>
                                            <div id="accordion23" class="collapse" data-parent="#accordion2">
                                                <div class="card-body">
                                                    <p class="ml-indi">Select Sum Insured</p>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio81">
                                                                <label class="custom-control-label lbl-indi" for="customRadio81"><span>5 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio82">
                                                                <label class="custom-control-label lbl-indi" for="customRadio82"><span>10 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio83">
                                                                <label class="custom-control-label lbl-indi" for="customRadio83"><span>15 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio84">
                                                                <label class="custom-control-label lbl-indi" for="customRadio84"><span>20 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6 mt-2">
                                    <div id="accordion2" class="according accordion-s2">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#accordion24">  <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="customCheck33">
                                                        <label class="custom-control-label" for="customCheck33"><span>Kid 2</span></label>
                                                    </div></a>
                                            </div>
                                            <div id="accordion24" class="collapse" data-parent="#accordion2">
                                                <div class="card-body">
                                                    <p class="ml-indi">Select Sum Insured</p>
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio91">
                                                                <label class="custom-control-label lbl-indi" for="customRadio91"><span>5 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio92">
                                                                <label class="custom-control-label lbl-indi" for="customRadio92"><span>10 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio93">
                                                                <label class="custom-control-label lbl-indi" for="customRadio93"><span>15 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="customRadio94">
                                                                <label class="custom-control-label lbl-indi" for="customRadio94"><span>20 Lakh</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-submit">Submit <i class="ti-check"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of individual -->

            <form action="<?php echo base_url() . "quotes/generate_proposal" ?>" id="hiddenQuoteForm" method="POST">
                <input type="hidden" name="plan_id" id="hiddenplanid" value="<?php echo $post_data['plan_id'] ;?>">
                <input type="hidden" name="cover" id="hiddencover" value="<?php echo $post_data['cover'] ;?>">
                <input type="hidden" name="premium" id="hiddenpremium" value="<?php echo$quote_info['self_data']['premium'];?> ">
                <input type="hidden" name="creditor_logo" id="hiddencreditorlogo" value="<?php echo $quote_info['policy_details'][0]['creditor_logo'] ;?>">
                <input type="hidden" name="policy_id" id="hiddenpolicyid" value="<?php echo $sub_id;?>">
                <input type="hidden" name="si_type_id" id="hiddensitypeid" value="<?php echo $post_data['si_type_id'];?>">
                <input type="hidden" style="display:none;">
            </form>
            <script>
                $("body").on("click", '.familyFloatBtn', function(e) {
                    debugger;
                    e.preventDefault();

                    // boolean variable
                    var allowSubmit = true;
                    var data = {};
                    var creditor_logo = '<?php echo $data_val['creditor_logo']?>';
                    var plan_id = $('#planid').val();

                    var arr  =[];
                    if ($('input[name="family_construct_arr[]"]:checked').length <=0)
                    { swal("Alert","Please select atleast one family construct", "warning");

                        return false;
                    }

                    $('input[name="family_construct_arr[]"]:checked').map(function(i){

                        arr[i] = {

                            "family_construct" :$(this).val(),
                            "is_adult" :$(this).data('kid'),

                        }

                    }).get();

                    var i = 1;
                    debugger;
                    var TableData = [];

                    $('input[name="chk_status[]"]:checked').each(function(row) {


                        var ids = $(this).attr('id');
                        var additional_plan = $(this).data('flag');

                        var policy_id = $(this).val();
                        var max_age = $('#max_age').val();

                        if(($('#sum_insured_'+i).val()) == 0 || $('#sum_insured_'+i).val() == "")
                        {

                            swal("Alert","Please select sum insured", "warning");

                            allowSubmit = false;
                            return false;
                        }
                        var sum_insured = $('#sum_insured_' + i).val();
                        if (allowSubmit) {

                            TableData[row] = {
                                "policy_id": policy_id,
                                "max_age": max_age,

                                "plan_id": plan_id,
                                "family_construct": arr,
                                "sum_insured": sum_insured,
                                "plan_flag": additional_plan
                            }
                        }
                        i++;

                    });
                    data.policy_details = TableData;
                    //console.log(data);
                    if (allowSubmit) {
                        $.ajax({
                            url: "/quotes/create_member_single_link",
                            type: "POST",
                            async: false,
                            data: data,
                            dataType: "json",
                            success: function (response) {
// return;
                                $('#editdetails-float').modal('hide');
                                var rep = response;
                                var res = JSON.parse(rep.data);
                                var cover_det = '';
                                var str = '';
                                var abc = [];
                                var sumIn = [];

                                if (res.status == 200) {
                                    $('.cover_det').html('');
                                    $('.coverNew').html('');
                                    $('.fam_con_det').text(res.member_type);
                                    $('.sub_type_name_det').text(res.sub_name_imp);
                                    $('.total_premium').text(res.total_premium);
                                    $('#hiddenpremium').val(res.total_premium);
                                    $('.plan_all_det').html('');

                                    for (i = 0; i < res.policy_det.length; i++) {
                                        //    $('.cover_det').html('');
                                        abc[i] = res.policy_det[i]['policy_id'];
                                        $('#hiddenplanid').val(res.policy_det[i].plan_id);


                                        if (res.policy_det[i]['sub_type_name'] != '' || res.policy_det[i]['sub_type_name'] != undefined) {
                                            cover_det = res.policy_det[i]['sub_type_name'] + " - " + res.policy_det[i]['sum_insured'];
                                            sumIn[i] = cover_det;
                                        }
                                        str = '<p class="text-cover"><img src="' + creditor_logo + '" width="25"> <span class="cover-lbl">' + res.policy_det[i]['sub_type_name'] + '</span></p>';
                                        str += '<p class="cover-premium"> Premium ' + res.policy_det[i]['premium'] + '<span class="amt-drop"><span class="incl">(incl gst)</span></span></p>';
                                        $(".plan_all_det").append(str);

                                        if (cover_det != undefined) {
                                            $('.cover_det').append(cover_det);
                                        }

                                    }
                                    abc.join(',');
                                    sumIn.join(',');
                                    $('#hiddenpolicyid').val(abc);
                                    $('#hiddencover').val(sumIn);

                                } else {
                                    swal("Alert", res.msg, "warning");
                                    return;
                                }
                            }


                        });
                    }

                });

                $(document).on("click", ".quoteBtn", function() {

                    if($('#customCheck').prop('checked') == true){
                        var data = {};
                        var sel_con = $('.fam_con_self').text();
                        var quote_info = '<?php echo $policy_id_imp;?>';

                        var total_premium = $('.total_premium').text();
                        if(total_premium == 0)
                        {
                            swal("Alert","No Premium found for selected family construct", "warning");
                            return false;

                        }
                        var cover = '<?php echo $post_data['cover'];?>';
                        if(cover == "" || cover == null){
                            var cover = $('#hiddencover').val();

                        }
                        var plan_id = $('#hiddenplanid').val();
                        // var data=[];
                        data.self_con = sel_con;
                        data.quote_info = quote_info;
                        data.cover = cover;
                        data.total_premium = total_premium;
                        data.plan_id = plan_id;
//console.log(data);return;
                        $.ajax({
                            url: "/quotes/Create_quote_self",
                            type: "POST",
                            async: false,
                            data:data,
                            dataType: "json",
                            success: function(response) {


                            }
                        });
                        $('#hiddenQuoteForm').submit();
                    }else{
                        swal("Alert", 'Please Select Product!', "warning");
                        return;
                    }


                });



            </script>
