<?php
namespace Core;

use Dotenv\Dotenv;

class PHXLogger {
    public static function displayError(string $message): void {
		
		if (!isset($_ENV['LOGGER_AI'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        }
        $apiKey = $_ENV['LOGGER_AI'];
		
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-neutral-800 text-neutral-200 h-screen gap-4 w-full flex flex-col justify-center items-center p-4">
            <div class="w-full flex flex-col max-w-6xl mx-auto rounded-md">
                <div class="flex">
                    <div class="text-red-600 text-2xl font-bold px-4 py-2 rounded-t-md bg-black border-b border-neutral-900">
                        ERROR
                    </div>
                </div>
                <div class="bg-neutral-950 p-6 rounded-tr-md rounded-b-md text-red-600 overflow-y-auto w-full">
                    ' . htmlspecialchars($message) . '
                    
                    <div class="flex mt-9">
                        <form id="generateForm">
                            <input type="text" id="inputText" name="inputText" value="Give me suggestions to improve it (Answer briefly): '. htmlspecialchars($message) .'" hidden required>
                            <button type="submit" id="suggestButton" class="px-4 py-2 bg-orange-800 hover:bg-orange-700 hover:duration-500 text-orange-100 rounded flex items-center">
                                Suggestion
                                <span id="loadingSpinner" class="hidden ml-2 spinner-border animate-spin w-4 h-4 border-t-2 border-orange-100 rounded-full"></span>
                            </button>
                        </form>
                    </div>
                    <div id="response" class="mt-4 text-neutral-200 text-sm"></div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/marked@15.0.7/lib/marked.umd.min.js"></script>
            <script>
            document.getElementById("generateForm").addEventListener("submit", function(event) {
                event.preventDefault();
                
                const inputText = document.getElementById("inputText").value;
                if (!inputText) {
                    alert("No error logs.");
                    return;
                }
                
                const suggestButton = document.getElementById("suggestButton");
                const loadingSpinner = document.getElementById("loadingSpinner");
                
                // Show loading spinner and disable the button
                suggestButton.disabled = true;
                loadingSpinner.classList.remove("hidden");
                
                const requestData = {
                    contents: [{
                        parts: [{
                            text: inputText
                        }]
                    }]
                };
                
                fetch("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey . '", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Response:", data);
                    
                    const candidates = data.candidates;
                    let resultText = "No content generated.";
                    if (candidates && candidates.length > 0) {
                        const parts = candidates[0].content.parts;
                        if (parts && parts.length > 0) {
                            resultText = parts[0].text;
                        }
                    }
                    
                    const htmlContent = marked.parse(resultText);
                    document.getElementById("response").innerHTML = htmlContent;
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById("response").innerHTML = "<strong>Error:</strong> Failed to generate content.";
                })
                .finally(() => {
                    // Hide loading spinner and re-enable the button
                    loadingSpinner.classList.add("hidden");
                    suggestButton.disabled = false;
                });
            });
            </script>
        </body>
        </html>
        ';
    }
}
?>
