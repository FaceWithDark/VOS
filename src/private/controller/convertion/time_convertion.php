<?php
    // Convert integer to 'HH:MM:SS' format
    function integerToTimeFormat($time) {
        $hours = floor($time / 3600);
        $minutes = floor(($time % 3600) / 60);
        $seconds = $time % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
