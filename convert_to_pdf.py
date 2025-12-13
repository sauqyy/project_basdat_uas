#!/usr/bin/env python3
"""
Script untuk mengkonversi Jupyter Notebook ke PDF
"""

import subprocess
import sys
import os

def convert_notebook_to_pdf(notebook_path, output_path=None):
    """
    Konversi Jupyter Notebook ke PDF
    
    Args:
        notebook_path (str): Path ke file .ipynb
        output_path (str): Path output PDF (optional)
    """
    try:
        # Periksa apakah file notebook ada
        if not os.path.exists(notebook_path):
            print(f"Error: File {notebook_path} tidak ditemukan!")
            return False
        
        # Buat command untuk konversi
        cmd = ["jupyter", "nbconvert", "--to", "pdf"]
        
        if output_path:
            cmd.extend(["--output", output_path])
        
        cmd.append(notebook_path)
        
        print(f"Mengkonversi {notebook_path} ke PDF...")
        print(f"Command: {' '.join(cmd)}")
        
        # Jalankan konversi
        result = subprocess.run(cmd, capture_output=True, text=True)
        
        if result.returncode == 0:
            print("✅ Konversi berhasil!")
            if result.stdout:
                print("Output:", result.stdout)
            return True
        else:
            print("❌ Konversi gagal!")
            print("Error:", result.stderr)
            return False
            
    except FileNotFoundError:
        print("❌ Error: jupyter nbconvert tidak ditemukan!")
        print("Install dengan: pip install nbconvert")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def install_requirements():
    """Install requirements untuk konversi PDF"""
    requirements = [
        "nbconvert",
        "pandoc",
        "jupyter"
    ]
    
    print("Installing requirements...")
    for req in requirements:
        try:
            subprocess.run([sys.executable, "-m", "pip", "install", req], 
                          check=True, capture_output=True)
            print(f"✅ {req} installed")
        except subprocess.CalledProcessError:
            print(f"❌ Failed to install {req}")

if __name__ == "__main__":
    # Path ke notebook
    notebook_path = "164221045_Fradinka_Amelia_Edyputri_Social_Media_Mining.ipynb"
    
    # Path output (optional)
    output_path = "Social_Media_Mining_Report.pdf"
    
    print("=== Jupyter Notebook to PDF Converter ===")
    print(f"Notebook: {notebook_path}")
    print(f"Output: {output_path}")
    print()
    
    # Install requirements
    install_requirements()
    print()
    
    # Konversi
    success = convert_notebook_to_pdf(notebook_path, output_path)
    
    if success:
        print(f"\n🎉 PDF berhasil dibuat: {output_path}")
    else:
        print("\n💡 Tips untuk troubleshooting:")
        print("1. Pastikan LaTeX terinstall (MiKTeX untuk Windows)")
        print("2. Coba: jupyter nbconvert --to webpdf notebook.ipynb")
        print("3. Atau gunakan: jupyter nbconvert --to html notebook.ipynb")

