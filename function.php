<?php
function createWavFile($leftChannelArray, $rightChannelArray, $filePath, $sampleRate = 44100, $bitsPerSample = 16) {
    // Ensure both channels have the same number of samples by padding the shorter array
    $maxLength = max(count($leftChannelArray), count($rightChannelArray));
    $leftChannelArray = array_pad($leftChannelArray, $maxLength, 0);
    $rightChannelArray = array_pad($rightChannelArray, $maxLength, 0);

    // WAV file header fields
    $audioFormat = 1; // PCM
    $numChannels = 2; // Stereo
    $byteRate = $sampleRate * $numChannels * $bitsPerSample / 8;
    $blockAlign = $numChannels * $bitsPerSample / 8;

    // Prepare audio data
    $audioData = '';
    for ($i = 0; $i < $maxLength; $i++) {
        $leftSample = isset($leftChannelArray[$i]) ? $leftChannelArray[$i] : 0;
        $rightSample = isset($rightChannelArray[$i]) ? $rightChannelArray[$i] : 0;

        // Adjust the samples based on the bit depth
        $maxValue = pow(2, $bitsPerSample - 1) - 1;
        $leftSample = max(-$maxValue, min($maxValue, $leftSample));
        $rightSample = max(-$maxValue, min($maxValue, $rightSample));

        // Pack the samples according to the specified bit depth
        if ($bitsPerSample == 16) {
            $audioData .= pack('vv', $leftSample, $rightSample);
        } elseif ($bitsPerSample == 24) {
            $packedLeftSample = pack('V', $leftSample & 0xFFFFFF | ($leftSample < 0 ? 0xFF000000 : 0));
            $packedRightSample = pack('V', $rightSample & 0xFFFFFF | ($rightSample < 0 ? 0xFF000000 : 0));
            $audioData .= substr($packedLeftSample, 0, 3) . substr($packedRightSample, 0, 3);
        } elseif ($bitsPerSample == 32) {
            $audioData .= pack('VV', $leftSample, $rightSample);
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