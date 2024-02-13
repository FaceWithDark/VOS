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

</head>
    <!-- Set backhround color -->
    <body>
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
                    </li>
                </ul>
            </nav>

            <!-- Indicate the 'section' part of the current HTML file. -->
            <section class="tournament-news">
                <!-- Custom class for styling the contents next to the navigation bar. -->
                <div class="information-box" id="annoucement-box">
                    <!-- Custom class for styling the title for each box. -->           
                    <div class="main-title">Vietnamese osu!taiko Tournament</div>
                    <!-- Customclass for styling the content part of each box -->
                    <p class="main-content">Welcome to the website of the biggest vietnamese osu!taiko tournament battlefield ! In here, you can look at everything related to VOT, with pages for all our current and past tournaments!</p>
                    <!-- <div class="youtube-iframe-container">
                        <iframe src="https://www.youtube.com/embed/aeUVQe7irW4?si=HmFiaAGYbrz5fCuR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div> -->
                </div>

                <div class="information-box">
                    <!-- Custom class for styling the title for each box. -->           
                    <div class="main-title">Vietnamese osu!taiko Tournament 3</div>
                    <!-- Customclass for styling the content part of each box -->
                    <p class="main-content">The Vietnamese osu!taiko Tournament 3 is our 3rd vietnamese-based osu!taiko tournament targeting a much larger players scale, from inf - #5000 !</p>
                </div>

                <div class="information-box">
                    <!-- Custom class for styling the title for each box. -->           
                    <div class="main-title">Vietnamese osu!taiko Tournament 2</div>
                    <!-- Customclass for styling the content part of each box -->
                    <p class="main-content">The Vietnamese osu!taiko Tournament 2 is our 2nd vietnamese-based osu!taiko tournament targeting a much larger players scale, from inf - #5000 !</p>
                </div>

                <div class="information-box">
                    <!-- Custom class for styling the title for each box. -->           
                    <div class="main-title">Vietnamese osu!taiko Tournament 1</div>
                    <!-- Customclass for styling the content part of each box -->
                    <p class="main-content">The Vietnamese osu!taiko Tournament 1 is our 1st vietnamese-based osu!taiko tournament targeting a much larger players scale, from inf - #5000 !</p>
                </div>
            </section>
        </form>

        <!-- Import JavaScript function file into the current HTML file. -->
        <script src="activeButton.js" type="text/JavaScript"></script>
    </body>
</html>