<?php

use OpenAI\Client;

function extract_skills($resume_text) {
    $apiKey = "YOUR_OPENAI_API_KEY"; // Replace with your OpenAI API key
    $client = new Client(['api_key' => $apiKey]);

    $prompt = "Extract all programming and technical skills from the following resume text. List only skills separated by commas.\n\nResume Text: " . $resume_text;

    $response = $client->completions()->create([
        'model' => 'gpt-4-mini',
        'prompt' => $prompt,
        'max_tokens' => 200
    ]);

    $skills = $response['choices'][0]['text'] ?? "";
    $skills = strtolower(trim($skills));
    $skills_array = array_map('trim', explode(',', $skills));
    $skills_array = array_filter($skills_array);

    return $skills_array;
}
?>