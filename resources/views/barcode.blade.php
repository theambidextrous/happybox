<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>HappyBox APIs Documentation</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                HappyBox APIs Barcodes
                <!-- <img src="{{ asset('media/default.png') }}"> -->
                </div>

                <div class="links">
<!-- <div class="container text-center" style="border: 1px solid #a1a1a1;padding: 15px;width: 70%;">
	<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQAAAAA8AQMAAACzc/ZHAAAABlBMVEX///8BhbYw9sP+AAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAADZJREFUOI1jOHD+/JnzB878+fOH58wZfiB94M8ZED4PxCA5hlEFowpGFYwqGFUwqmBUwWBSAAAJmPmYNXcnewAAAABJRU5ErkJggg==" alt="barcode" />
    <br>
    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASAAAAA8AQMAAAD8LvWXAAAABlBMVEX///8BhbYw9sP+AAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAADhJREFUSIntyjENADAMA8EAiFT+bALAkgl5aIOiyw8vD74aWx4laem8nWjzZvX+BQKBQCAQCPQHXcR5jU+gQNTZAAAAAElFTkSuQmCC" alt="barcode" />
	<img src="data:image/png;base64,{{DNS1D::getBarcodePNG('12', 'C39+')}}" alt="barcode" />
	<img src="data:image/png;base64,{{DNS1D::getBarcodePNG('13', 'C39E')}}" alt="barcode" />
	<img src="data:image/png;base64,{{DNS1D::getBarcodePNG('14', 'C39E+')}}" alt="barcode" />
	<img src="data:image/png;base64,{{DNS1D::getBarcodePNG('15', 'C93')}}" alt="barcode" /> 
    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG('4', 'C39+',3,33,array(50,205,50))}}" alt="barcode"/>
	<br/>
</div> -->
                </div>
            </div>
        </div>
    </body>
</html>
