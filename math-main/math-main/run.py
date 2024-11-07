import pickle
import subprocess

# Spécifiez le chemin relatif ou absolu de votre fichier
chemin_script = 'D:/math/math/p4ob.py'  # Chemin relatif
# Ou
# chemin_script = 'D:\\math\\math\\p4.ob.py'  # Chemin absolu

# Exécute le script en utilisant subprocess.run()
for i in range(5000):
    subprocess.run(['python', chemin_script])
    print(i)
