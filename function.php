<?php
function createWavFile($inputArray, $filePath, $sampleRate = 44100, $bitsPerSample = 16) {
    // WAV file header fields
    $audioFormat = 1; // PCM
    $numChannels = 2; // Stereo

    // Calculate byte rate and block align based on user input
    $byteRate = $sampleRate * $numChannels * $bitsPerSample / 8;
    $blockAlign = $numChannels * $bitsPerSample / 8;

    // Prepare audio data
    $audioData = '';
    foreach ($inputArray as $sample) {
        // Adjust the sample based on the bit depth
        $maxValue = pow(2, $bitsPerSample) / 2 - 1;
        $sample = max(-$maxValue, min($maxValue, $sample));
        // Pack the sample according to the specified bit depth
        if ($bitsPerSample == 16) {
            $audioData .= pack('vv', $sample, $sample); // Stereo: duplicate the sample for both channels
        } elseif ($bitsPerSample == 24) {
            $packedSample = pack('V', $sample & 0xFFFFFF | ($sample < 0 ? 0xFF000000 : 0)); // 24-bit sample packed into 32 bits
            $audioData .= substr($packedSample, 0, 3) . substr($packedSample, 0, 3); // Stereo: duplicate the sample for both channels
        } elseif ($bitsPerSample == 32) {
            $audioData .= pack('VV', $sample, $sample); // Stereo: duplicate the sample for both channels
        }
    }

    // Calculate data and file sizes
    $dataSize = strlen($audioData);
    $fileSize = 36 + $dataSize;

    // Construct the WAV file header
    $header = 'RIFF' . pack('V', $fileSize) . 'WAVE' .
              'fmt ' . pack('V', 16) . pack('v', $audioFormat) . pack('v', $numChannels) .
              pack('V', $sampleRate) . pack('V', $byteRate) . pack('v', $blockAlign) .
              pack('v', $bitsPerSample) .
              'data' . pack('V', $dataSize);

    // Save the WAV file to the specified path
    file_put_contents($filePath, $header . $audioData);
}
?>