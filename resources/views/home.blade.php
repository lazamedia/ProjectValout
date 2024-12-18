<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@200..800&family=Quantico:ital,wght@0,400;0,700;1,400;1,700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Tilt+Neon&display=swap" rel="stylesheet">

    <title>Home</title>
    <style>
        body{
            font-family: 'Quantico';
            background-color: #060716;
            color: #ffff;
            margin: 0;
            box-sizing: border-box;
        }
        .box-container{
            text-align: center;
            align-content: center;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        h1{
            font-size: 31pt;
            margin: 0;
            color: #11b4f5;
        }
        p{
            margin: 0%;
        }
        .bawah{
            bottom: 0;
            position: absolute;
            text-align: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
        }
        .bawah img{
            width: 100px;
            margin-bottom: 0px;
        }
        .box-btn{
            display: flex;
            gap: 20px;
            align-content: center;
            align-items: center;
            justify-content: center;
            margin-top: 40px;
        }
        .box-btn a{
            text-decoration: none;
            padding: 6px 15px;
            color: #060716;
            background-color: #11b4f5;
            border: 1px solid #11b4f5;
            border-radius: 3px;
            font-weight: 100;
            font-size: 11pt;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 0px 9px rgba(14, 199, 174, 0.637);
        }
        .box-btn a:hover{
            color: #11b4f5;
            background-color: #060716;
            border: 1px solid #11b4f5;
            transition: all 0.3s ease-in-out;
        }

        svg {
        width: 100px;
        height: auto;
        fill: #11b4f5;
        bottom: 0;
        stroke-width: 2px;
        transition: transform 0.3s ease-in-out, fill 0.3s ease-in-out;
        }
        svg:hover {
            fill: #0ef7e5; 
        }
        @media (max-width:780px){
            h1{
                font-size: 25pt;
            }
        }
    </style>
</head>
<body>
    

    <div class="box-container">
        <h1>UKM CYBER CLASS</h1>
        <p>#NeverStopLearning</p>
        <a href=""></a>
        <a href=""></a>
        <div class="box-btn">
            @guest
            <!-- Tampilkan tombol Login jika pengguna belum login -->
            <a href="/login">Login</a>
            @endguest
        
            @auth
                <!-- Tampilkan tombol Dashboard jika pengguna sudah login -->
                <a href="/user">Dashboard</a>
            @endauth
        </div>
        <div class="bawah">
            <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="143.089mm" height="108.783mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
            viewBox="0 0 1286 977"
            xmlns:xlink="http://www.w3.org/1999/xlink">
            <g id="Layer_x0020_1">
            <metadata id="CorelCorpID_0Corel-Layer"/>
            <path class="fil0" d="M711 866c-8,13 -29,15 -39,26 -8,9 -14,32 -33,30 -16,-1 -19,-21 -32,-32 -14,-12 -32,-10 -38,-28 -19,-63 73,-39 77,-39 17,-1 30,-13 52,0 9,5 23,27 13,43zm-368 -285c68,-12 122,22 144,62 13,24 20,68 2,85 -19,16 -67,9 -95,7 -38,-2 -72,4 -107,3 -93,-2 -42,-104 -17,-125 19,-16 42,-27 73,-32zm543 1c124,-26 188,95 156,139 -17,23 -57,17 -89,15 -89,-6 -167,26 -169,-42 -2,-63 48,-100 102,-112zm-810 395l1160 0c38,-209 -7,-223 3,-310 3,-23 3,-46 1,-69 -7,-91 -13,-18 10,-109 45,-181 44,-304 14,-489 -69,10 -119,43 -158,78l-113 120c-116,141 -168,38 -357,45 -36,1 -72,6 -106,13 -26,6 -72,23 -94,22 -70,-5 -166,-106 -201,-152 -48,-64 -138,-114 -224,-122 -4,45 -11,83 -11,131 -1,118 22,246 58,355 28,84 23,33 12,107 -7,47 6,93 1,130 -14,112 -10,143 5,250z"/>
            <path class="fil0" d="M905 585c-67,17 -40,117 27,96 21,-7 40,-34 33,-60 -6,-24 -31,-44 -60,-36z"/>
            <path class="fil0" d="M318 644c11,58 112,47 98,-22 -12,-58 -111,-46 -98,22z"/>
            </g>
            </svg>
        </div>
    </div>


</body>
</html>