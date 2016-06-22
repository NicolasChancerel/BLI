#importation librairies
import time
import RPi.GPIO as GPIO
from utilities import *

###########################################
#####      DEBUT LECTUREDISTANCE      #####
###########################################




def lectureDistance(GPIO_TRIGGER, GPIO_ECHO):

    t_distance = 0
    OK = True
    for i in range(5):
        # envoi une impulsion de 10us au trigger
        GPIO.output(GPIO_TRIGGER, True)
        time.sleep(0.00001)
        GPIO.output(GPIO_TRIGGER, False)
        h_lancement = time.time()
        start = time.time() #sauvegarde l heure precise de la fin de l envoi
        while GPIO.input(GPIO_ECHO)==0:
            start = time.time()
            if (time.time() - h_lancement) > 1:
                OK = False
                break       
        while GPIO.input(GPIO_ECHO)==1:
            stop = time.time() #sauvegarde l heure precise jusqu a la fin de la reception
        
        try:    
            # Calcul le temps ecoule entre l'envoi et la reception
            elapsed = stop-start

            # Distance pulse travelled in that time is time
            # multiplied by the speed of sound (cm/s)
            distance = elapsed * 34000

            # That was the distance there and back so halve the value
            distance = distance / 2
            t_distance += distance
            time.sleep(0.02)
        except:
            print("[\033[01;31mX\033[m] Erreur with STOP") 
            break
  
    t_distance = t_distance/5
    return t_distance
###########################################
#####       FIN LECTUREDISTANCE       #####
###########################################