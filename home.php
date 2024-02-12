<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>

    <!-- Import CSS styling file into the current HTML file. -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Import Boxicons icons into the current HTML file. -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .information-box {
            position: relative;
            background-color: lightgreen;
            min-height: 10vh;
            left: 80px;
            transition: all 0.5s ease;
            width: calc(100% - 80px);
            padding-top: 1rem;
            margin: 1.5rem 0;
            border-radius: 1rem;
            z-index: -1;
        }

        .information-box .main-title {
            font-weight: bold;
            text-transform: uppercase;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .information-box .main-context {
            background-color: lightblue;
        }

        /* .youtube-iframe-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            height: auto;
            overflow: hidden;
        }

        .youtube-iframe-container iframe {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        } */
    </style>
</head>
    <!-- Set backhround color -->
    <body style="background-color: #367588;">
        <form action="index.php" method="post">
            <!-- Indicate the 'navigation' part of the current HTML file. -->
            <nav>
                <!-- Custom class for styling the 'top part' of the navigation bar. -->
                <div class="top-navigation-bar">
                    <!-- Import custom Boxicons icons' class. -->
                    <!-- Bold the wanted text (less bolded than <h1> tag). -->
                    <i class="bx bxs-navigation"><strong>Navigate</strong></i>
                </div>

                <!-- Import custom Boxicons icons' class -->
                <!-- Custom 'id' class for styling when clicked on the 'menu' icon. -->
                <i class="bx bx-menu" id="click-button"></i>

                <!-- Custom class for styling users profile. -->
                <div class="users">
                    <!-- Customize the current imported image to fit with the wanted navigation bar. -->
                    <img src="#" alt="me" class="users-image">
                    <!-- TODO: Use PHP to call player name's after API passed. -->
                    <p>Insert name here</p>
                </div>

                <!-- Create an un-ordered list of elements. -->
                <ul>
                    <!-- Formatting elements into dot-point listed style. -->
                    <li>
                        <!-- Direct links. -->
                        <a href="home.php">
                            <!-- Import custom Boxicons icons' class. -->                            
                            <i class="bx bxs-grid-alt"></i>
                            <!-- Custom class to style 'Home' click button. -->
                            <span class="navigation-links">Home</span>
                        </a>
                        <!-- Custom class to style 'Home' guide tip when re-sized 'menu' button. -->
                        <span class="small-navigation-links">Home</span>
                    </li>
        
                    <!-- Formatting elements into dot-point listed style. -->
                    <li>
                        <!-- Direct links. -->
                        <a href="archive.php">
                            <!-- Import custom Boxicons icons' class. -->
                            <i class="bx bxs-box"></i>
                            <!-- Custom class to style 'Archive' click button. -->
                            <span class="navigation-links">Archive</span>
                        </a>
                        <!-- Custom class to style 'Archieve' guide tip when re-sized 'menu' button. -->
                        <span class="small-navigation-links">Archive</span>
                    </li>
                    
                    <!-- Formatting elements into dot-point listed style. -->
                    <li>
                        <!-- Direct links. -->
                        <a href="staff.php">
                            <!-- Import custom Boxicons icons' class. -->
                            <i class="bx bxs-phone"></i>
                            <!-- Custom class to style 'Staff' click button. -->
                            <span class="navigation-links">Staff</span>
                        </a>
                        <!-- Custom class to style 'Staff' guide tip when re-sized 'menu' button. -->
                        <span class="small-navigation-links">Staff</span>
                    </li>
 
                    <!-- Formatting elements into dot-point listed style. -->
                    <li>
                        <!-- Direct links. -->
                        <a href="song.php">
                            <!-- Import custom Boxicons icons' class. -->
                            <i class="bx bxs-music"></i>
                            <!-- Custom class to style 'Song' click button. -->
                            <span class="navigation-links">Song</span>
                        </a>
                        <!-- Custom class to style 'Song' guide tip when re-sized 'menu' button. -->
                        <span class="small-navigation-links">Song</span>
                    </li>
                </ul>
            </nav>

            <section class="container">
                <div class="information-box" id="annoucement-box">
                    <div class="main-title">Vietnamese osu!taiko Tournament</div>
                    <div class="main-context">Welcome to the website of the biggest vietnamese osu!taiko tournament battlefield ! In here, you can look at everything related to VOT, with pages for all our current and past tournaments!
                        <!-- <div class="youtube-iframe-container">
                            <iframe src="https://www.youtube.com/embed/aeUVQe7irW4?si=HmFiaAGYbrz5fCuR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div> -->
                    </div>
                </div>

                <div class="information-box">
                    <div class="main-title">Vietnamese osu!taiko Tournament 3</div>
                    <div class="main-context">
                        <p>The Vietnamese osu!taiko Tournament 3 is our 3rd vietnamese-based osu!taiko tournament targeting a much larger players scale, from inf - #5000 !</p>
                    </div>
                </div>

                <div class="information-box">
                    <div class="main-title">Vietnamese osu!taiko Tournament 2</div>
                    <div class="main-context">
                        <p>The Vietnamese osu!taiko Tournament 2 is our 2nd vietnamese-based osu!taiko tournament targeting a much larger players scale, from inf - #5000 !</p>
                    </div>
                </div>
                
                <div class="information-box">
                    <div class="main-title">Vietnamese osu!taiko Tournament 1</div>
                    <div class="main-context">
                        <p>The Vietnamese osu!taiko Tournament 1 is our 1st vietnamese-based osu!taiko tournament targeting a much larger players scale, from inf - #5000 !</p>
                    </div>
                </div>
            </section>
        </form>

        <!-- Import JavaScript function file into the current HTML file. -->
        <script src="activeButton.js" type="text/JavaScript"></script>
    </body>
</html>