# authentication/utils.py
import hashlib
import hmac
import os
import binascii
import time
import jwt
from django.conf import settings
from typing import Tuple

import os
import hashlib
import binascii

# ----------------------
# PASSWORD HELPERS
# ----------------------

def hash_password(password: str, iterations: int = 200_000) -> str:
    """
    Hash a password with PBKDF2-HMAC-SHA256.
    Returns a string formatted as: iterations$salt$hash
    - Salt is 16 random bytes, hex-encoded.
    - Hash is hex-encoded.
    """
    salt = os.urandom(16)
    dk = hashlib.pbkdf2_hmac(
        'sha256',
        password.encode('utf-8'),
        salt,
        iterations
    )
    return f"{iterations}${binascii.hexlify(salt).decode()}${binascii.hexlify(dk).decode()}"


def verify_password(password: str, stored: str) -> bool:
    """
    Verify a password against the stored hash string.
    """
    try:
        iterations_str, salt_hex, hash_hex = stored.split('$')
        iterations = int(iterations_str)
        salt = binascii.unhexlify(salt_hex)
        expected_hash = binascii.unhexlify(hash_hex)

        # Compute hash of the input password using same salt & iterations
        dk = hashlib.pbkdf2_hmac(
            'sha256',
            password.encode('utf-8'),
            salt,
            iterations
        )

        # Use compare_digest to prevent timing attacks
        return hmac.compare_digest(dk, expected_hash)
    except (ValueError, TypeError, binascii.Error):
        # Catch split errors, unhexlify errors, or invalid stored format
        return False



# JWT HELPERS (requires PyJWT)
def create_jwt(payload: dict, exp_seconds: int = None) -> str:
    """
    Create a signed JWT with user info / roles.
    """
    if exp_seconds is None:
        exp_seconds = getattr(settings, "JWT_EXP_DELTA_SECONDS", 60*60*24)
    now = int(time.time())
    data = {
        "iat": now,
        "exp": now + exp_seconds,
        **payload
    }
    token = jwt.encode(data, settings.JWT_SECRET, algorithm=getattr(settings, "JWT_ALGORITHM", "HS256"))
    # pyjwt v2 returns str, older versions may return bytes
    if isinstance(token, bytes):
        token = token.decode()
    return token

def decode_jwt(token: str) -> dict:
    """
    Decode and verify a token. Raises jwt exceptions on failure.
    """
    return jwt.decode(token, settings.JWT_SECRET, algorithms=[getattr(settings, "JWT_ALGORITHM", "HS256")])
