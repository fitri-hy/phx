<?php
namespace Core;

use ErrorException;
use Dotenv\Dotenv;

class PHXLogger {
    public static function Logger(): void {
        static $initialized = false;
        if ($initialized) return;
        $initialized = true;

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
		
		if (!isset($_ENV['LOGGER_AI'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        }

    }

    public static function handleError(int $severity, string $message, string $file, int $line): void {
        if (!(error_reporting() & $severity)) {
            return;
        }
        $snippet = self::getCodeSnippet($file, $line);
        self::displayError("Error: {$message} in {$file} on line {$line}\n\n{$snippet}");
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(\Throwable $exception): void {
        $snippet = self::getCodeSnippet($exception->getFile(), $exception->getLine());
        self::displayError("Uncaught Exception: " . $exception->getMessage() . 
            " in " . $exception->getFile() . 
            " on line " . $exception->getLine() . "\n\n{$snippet}");
    }

    public static function handleShutdown(): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $snippet = self::getCodeSnippet($error['file'], $error['line']);
            self::displayError("Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}\n\n{$snippet}");
        }
    }
	
    private static function getCodeSnippet(string $file, int $line): string {
        if (!file_exists($file)) {
            return "Unable to retrieve code snippet (file not found).";
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $totalLines = count($lines);

        $start = max(0, $line - 2);
        $end = min($totalLines - 1, $line);

        $snippet = [];
        for ($i = $start; $i <= $end; $i++) {
            $prefix = ($i + 1 === $line) ? '>> ' : '   ';
            $snippet[] = $prefix . ($i + 1) . ": " . $lines[$i];
        }

        return implode("\n", $snippet);
    }
	
	public static function displayError(string $message): void {
		$apiKey = $_ENV['LOGGER_AI'];
        echo '
		<!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <script src="https://cdn.tailwindcss.com"></script>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
			<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
        </head>
        <body class="bg-neutral-800 text-neutral-200 h-screen gap-4 w-full flex flex-col justify-center items-center p-4">
            <div class="w-full flex flex-col max-w-6xl mx-auto rounded-md">
                <div class="flex">
                    <div class="text-red-600 text-2xl font-bold px-6 py-2 rounded-t-md bg-black">
                        ERROR
                    </div>
                </div>
                <div class="bg-neutral-950 p-6 rounded-tr-md rounded-b-md text-red-600 overflow-y-auto w-full">
                    <div>
						<pre><code>' . htmlspecialchars($message) . '</code></pre>
					</div>
                    <div class="flex mt-4">
                        <form id="generateForm">
                            <input type="text" id="inputText" name="inputText" value="Give me suggestions to improve it (Answer briefly): '. htmlspecialchars($message) .'" hidden required>
                            <button type="submit" id="suggestButton" class="text-xs py-1 px-2 bg-emerald-700 hover:bg-emerald-600 hover:duration-500 text-emerald-100 rounded-md flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
								  <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
								</svg>
								Help
                                <span id="loadingSpinner" class="hidden ml-2 spinner-border animate-spin w-4 h-4 border-t-2 border-orange-100 rounded-full"></span>
                            </button>
                        </form>
                    </div>
                    <div id="response" class="mt-4 text-neutral-200 text-sm"></div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/marked@15.0.7/lib/marked.umd.min.js"></script>
            <script>
			document.addEventListener("DOMContentLoaded", (event) => {
				hljs.highlightAll();
			  });
  
            document.getElementById("generateForm").addEventListener("submit", function(event) {
                event.preventDefault();
                
                const inputText = document.getElementById("inputText").value;
                if (!inputText) {
                    alert("No error logs.");
                    return;
                }
                
                const suggestButton = document.getElementById("suggestButton");
                const loadingSpinner = document.getElementById("loadingSpinner");
                
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
