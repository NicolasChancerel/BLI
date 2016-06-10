import time, os, serial
import RPi.GPIO as GPIO
from utilities import *
from decimal import *
from distance import *


clear = lambda: os.system('clear')
green = '\033[01;32m'
red = '\033[01;31m'
native = '\033[m'


def spinning_cursor():
    while True:
        for cursor in '|/-\\|||':
            yield cursor



def lectureDistanceFondBLI(GPIO_TRIGGER, GPIO_ECHO):
    spinner = spinning_cursor()
    distanceFondBLI = 0    #initialise le fond a 0
    start = time.time()

    
    for i in range(30):
    
        sys.stdout.write(spinner.next())
        sys.stdout.flush()
        time.sleep(0.1)
        sys.stdout.write('\b')
        # Send 10us pulse to trigger
        GPIO.output(GPIO_TRIGGER, True)
        time.sleep(0.00001)
        GPIO.output(GPIO_TRIGGER, False)
        startF = time.time()
        while GPIO.input(GPIO_ECHO)==0:
           startF = time.time()

        while GPIO.input(GPIO_ECHO)==1:
           stopF = time.time()

        # Calculate pulse length
        elapsed = stopF-startF

        # Distance pulse travelled in that time is time
        # multiplied by the speed of sound (cm/s)
        distance = elapsed * 34000

        # That was the distance there and back so halve the value
        distance = distance / 2
        
        distanceFondBLI += distance      #ajoute a chaque passage dans la boucle la distance recuperer

    distanceFondBLI = distanceFondBLI/30
    sys.stdout.flush()
    
    stop = time.time()
    sys.stdout.write(green + "OK " + native)
    sys.stdout.write("[")
    tempsEcoule = str(stop - start)
    sys.stdout.write(tempsEcoule)
    sys.stdout.write("] Calcul fond Boite aux lettre :")
    sys.stdout.write(str(distanceFondBLI) + "\n")
    
    return distanceFondBLI    #divise par 10 les ditance recuperer pour trouver une moyenne

    
def gpioSetup(GPIO_TRIGGER,GPIO_ECHO):
    start = time.time()
    # reglage pin global
    GPIO.setwarnings(False)
    GPIO.setmode(GPIO.BOARD)
    # reglage pin distance
    GPIO.setup(GPIO_TRIGGER,GPIO.OUT)  # Trigger
    GPIO.setup(GPIO_ECHO,GPIO.IN)      # Echo
    GPIO.output(GPIO_TRIGGER, False)
    
    stop = time.time()
    sys.stdout.write(green + "OK " + native)
    sys.stdout.write("[")
    tempsEcoule = str(stop - start)
    sys.stdout.write(tempsEcoule)
    sys.stdout.write("] Initialisation port GPIO\n")
    
    
def serialSetup(portUsbRFID, BITRATE):
    start = time.time()
    serialRFID = serial.Serial(portUsbRFID, BITRATE)
    stop = time.time()
    sys.stdout.write(green + "OK " + native)
    sys.stdout.write("[")
    tempsEcoule = str(stop - start)
    sys.stdout.write(tempsEcoule)
    sys.stdout.write("] Initialisation port Serial\n")
    return serialRFID
    
    
def BLI():
    print("\033[01;32m#")*88
    print("#                                                                                      #")
    print("#   oooooooooo.   ooooo         ooooo   |                                              #")
    print("#   `888'   `Y8b  `888'         `888'   |   Version BLI: 0.8    Version Python 4.9.2   #")
    print("#    888     88P   888           888    |                                              #")
    print("#    888oooo888    888           888    |    Author : Chancerel Nicolas                #")       
    print("#    888    `88b   888           888    |             Jerome Ullmann                   #") 
    print("#    888    .88P   888      o    888    |             Jean-Pierre Cheney               #")
    print("#   o888bood8P'   o888oooood8   o888o   |                                              #")
    print("#                                                                                      #")
    print("#")*88

def done():
    print("\033[01;32m#")*88
    print("# BLI is started, and ready to work !")
    print(" \033[m")

    
    
    
    
    
    
    
    
    
    
    