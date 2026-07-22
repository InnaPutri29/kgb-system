import os
import re

search_dir = r"d:\Downloads\PROYEK MANDIRI\kgb-system\resources\views\admin"

# Regex pattern to match max-w-* mx-auto or similar layout classes on the main wrapper
pattern1 = re.compile(r'class="(?:max-w-[a-zA-Z0-9-]+\s+mx-auto\s+space-y-[0-9]+|space-y-[0-9]+\s+max-w-[a-zA-Z0-9-]+\s+mx-auto)"')
pattern2 = re.compile(r'class="space-y-5"')

for root, dirs, files in os.walk(search_dir):
    for file in files:
        if file.endswith(".blade.php"):
            filepath = os.path.join(root, file)
            with open(filepath, "r", encoding="utf-8") as f:
                content = f.read()
            
            new_content = content
            new_content = pattern1.sub('class="space-y-6"', new_content)
            
            # For pegawai/index.blade.php which had space-y-5
            if file == "index.blade.php" and "pegawai" in filepath:
                new_content = pattern2.sub('class="space-y-6"', new_content)
                
            if new_content != content:
                with open(filepath, "w", encoding="utf-8") as f:
                    f.write(new_content)
                print(f"Updated {filepath}")
