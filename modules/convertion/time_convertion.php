<?php
    // Convert 'HH:MM:SS' format to seconds
    function durationToSeconds($duration) {
        $parts = explode(':', $duration);
        return count($parts) === 3 ? ($parts[0] * 3600 + $parts[1] * 60 + $parts[2]) : 0;
    }
