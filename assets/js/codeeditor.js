

        let editor;
        // Initialize CodeMirror
        document.addEventListener('DOMContentLoaded', function() {
            editor = CodeMirror.fromTextArea(document.getElementById('codeEditor'), {
                mode: 'python',
                theme: 'dracula',
                lineNumbers: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                indentUnit: 4,
                tabSize: 4,
                indentWithTabs: false
            });
            
            editor.setValue(`def main():
    # Write your Python 3 code here
    print("Hello, World!")

if __name__ == "__main__":
    main()`);
        });

        // Language Change
        function changeLanguage() {
    const lang = document.getElementById('languageSelect').value;

    const modes = {
        'python': 'python',
        'python2': 'python',
        'java': 'text/x-java',
        'cpp': 'text/x-c++src',
        'c': 'text/x-csrc',
        'csharp': 'text/x-csharp',
        'javascript': 'javascript',
        'typescript': 'text/typescript',
        'php': 'application/x-httpd-php',
        'go': 'text/x-go',
        'kotlin': 'text/x-kotlin',
        'rust': 'text/x-rustsrc',
        'ruby': 'text/x-ruby',
        'swift': 'text/x-swift',
        'r': 'text/x-rsrc',
        'scala': 'text/x-scala'
    };

    const templates = {
        'python': `def main():
    # Write your Python 3 code here
    print("Hello, World!")

if __name__ == "__main__":
    main()`,

        'python2': `def main():
    # Write your Python 2 code here
    print "Hello, World!"

if __name__ == "__main__":
    main()`,

        'java': `public class Main {
    public static void main(String[] args) {
        // Write your Java code here
        System.out.println("Hello, World!");
    }
}`,

        'cpp': `#include <iostream>
using namespace std;

int main() {
    // Write your C++ code here
    cout << "Hello, World!" << endl;
    return 0;
}`,

        'c': `#include <stdio.h>

int main() {
    // Write your C code here
    printf("Hello, World!\\n");
    return 0;
}`,

        'csharp': `using System;

class Program {
    static void Main() {
        // Write your C# code here
        Console.WriteLine("Hello, World!");
    }
}`,

        'javascript': `// Write your JavaScript code here
console.log("Hello, World!");`,

        'typescript': `// Write your TypeScript code here
function main(): void {
    console.log("Hello, World!");
}

main();`,

        'php': `<?php
// Write your PHP code here
echo "Hello, World!";
?>`,

        'go': `package main
import "fmt"

func main() {
    // Write your Go code here
    fmt.Println("Hello, World!")
}`,

        'kotlin': `fun main() {
    // Write your Kotlin code here
    println("Hello, World!")
}`,

        'rust': `fn main() {
    // Write your Rust code here
    println!("Hello, World!");
}`,

        'ruby': `# Write your Ruby code here
puts "Hello, World!"`,

        'swift': `import Foundation

// Write your Swift code here
print("Hello, World!")`,

        'r': `# Write your R code here
print("Hello, World!")`,

        'scala': `object Main {
    def main(args: Array[String]): Unit = {
        // Write your Scala code here
        println("Hello, World!")
    }
}`
    };

    // Tahrirchi tilini o‘zgartirish (agar mavjud bo‘lsa)
    if (editor) {
        editor.setOption('mode', modes[lang] || 'text/plain');
        editor.setValue(templates[lang] || '// No template available for this language');
    }
}

