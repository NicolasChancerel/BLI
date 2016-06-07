#importation librairies
import time
import RPi.GPIO as GPIO
from utilities import *

def lectureDistance(GPIO_TRIGGER, GPIO_ECHO):

    # envoi une impulsion de 10us au trigger
    GPIO.output(GPIO_TRIGGER, True)
    time.sleep(0.00001)
    GPIO.output(GPIO_TRIGGER, False)
    start = time.time()
    
    while GPIO.input(GPIO_ECHO)==0:
       start = time.time()

    while GPIO.input(GPIO_ECHO)==1:
       stop = time.time()

    # Calcul le temps ecoule entre l'envoi et la reception
    elapsed = stop-start

    # Distance pulse travelled in that time is time
    # multiplied by the speed of sound (cm/s)
    distance = elapsed * 34000

    # That was the distance there and back so halve the value
    distance = distance / 2

    return distance