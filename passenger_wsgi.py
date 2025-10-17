import sys, os
# Ganti 'myproject' dengan nama folder project kamu di hosting
sys.path.insert(0, os.path.dirname(__file__))

from app import app as application
