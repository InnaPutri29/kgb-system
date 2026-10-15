import os
import re

directory = r'd:\Downloads\PROYEK MANDIRI\kgb-system'

replacements = [
    (r'SkpEvaluasi', r'PkpEvaluasi'),
    (r'skpEvaluasi', r'pkpEvaluasi'),
    (r'skp_evaluasi', r'pkp_evaluasi'),
    (r'file_bukti_skp', r'file_bukti_pkp'),
    (r'\$skp', r'$pkp'),
    (r'/\{skp\}', r'/{pkp}'),
    (r"'skp'", r"'pkp'"),
    (r'"skp"', r'"pkp"'),
    (r'skpTerakhir', r'pkpTerakhir'),
    (r'skpPeriodeBerjalan', r'pkpPeriodeBerjalan'),
    (r'skpList', r'pkpList'),
    (r'Sasaran Kinerja Pegawai', r'Predikat Kinerja Pegawai'),
    (r'SKP', r'PKP')
]

for root, dirs, files in os.walk(directory):
    if 'vendor' in root or 'node_modules' in root or '.git' in root or 'storage' in root:
        continue
    for file in files:
        if file.endswith('.php') or file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            new_content = content
            for old, new in replacements:
                # Need to use replace for exact strings or properly escape regex
                # Let's just use replace to be safe and avoid regex escape issues
                pass
            
            # Using simple replace
            new_content = new_content.replace('SkpEvaluasi', 'PkpEvaluasi')
            new_content = new_content.replace('skpEvaluasi', 'pkpEvaluasi')
            new_content = new_content.replace('skp_evaluasi', 'pkp_evaluasi')
            new_content = new_content.replace('file_bukti_skp', 'file_bukti_pkp')
            new_content = new_content.replace('$skp', '$pkp')
            new_content = new_content.replace('/{skp}', '/{pkp}')
            new_content = new_content.replace("'skp'", "'pkp'")
            new_content = new_content.replace('"skp"', '"pkp"')
            new_content = new_content.replace('skpTerakhir', 'pkpTerakhir')
            new_content = new_content.replace('skpPeriodeBerjalan', 'pkpPeriodeBerjalan')
            new_content = new_content.replace('skpList', 'pkpList')
            new_content = new_content.replace('Sasaran Kinerja Pegawai', 'Predikat Kinerja Pegawai')
            new_content = new_content.replace('SKP', 'PKP')
            new_content = new_content.replace('.skp', '.pkp') # for view names like pegawai.skp

            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                print(f"Updated: {filepath}")
