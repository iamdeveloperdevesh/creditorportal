# Hello World program in Python
    

from Crypto.Cipher import AES
 
from base64 import b64decode, b64encode
 
import json
 

BLOCK_SIZE = AES.block_size
# which is 16
 

def pad(s):
 
    return s + (BLOCK_SIZE -len(s) % BLOCK_SIZE) * chr(BLOCK_SIZE - len(s) % BLOCK_SIZE)
 

def unpad(s):
 
    return s[: -ord(s[len(s) - 1 :])]
# noqa: E203
 

class MaxLifeCipher:
 
    def _init_(self,secretkey):
 
# AES required only 16 chars IV
 
        self.key = secretkey[0:16]
# Key
 
        self.iv = secretkey[0:16]
# offset
 
 

    def encrypt(self,json_body):
 
        message =json.dumps(json_body,indent=4).replace("\n","\r\n")
 
        text =pad(message).encode()
 
        cipher =AES.new(key=self.key.encode(),mode=AES.MODE_CBC,IV=self.iv.encode())
 
        encrypted_text =cipher.encrypt(text)
 
        return b64encode(encrypted_text).decode()
 
 

def decrypt(self,encrypted_text):
 
    encrypted_text =b64decode(encrypted_text)
 
    cipher =AES.new(key=self.key.encode(),
mode=AES.MODE_CBC,IV=self.iv.encode())
 
    decrypted_text = cipher.decrypt(encrypted_text)
 
    json_string = unpad(decrypted_text).decode()
    return json.loads(json_string)