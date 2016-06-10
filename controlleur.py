#############################################################################
#                      BOITE AUX LETTRES INTELIGENTE                        #
#############################################################################
# Date : 10/07/2016
# Version BLI : 0.8            Version Python : 2.4.2
#
#
#
#


#importation des librairies utilise
import serial
from threading import Thread
import re, sys, signal, os, datetime, time, requests
import RPi.GPIO as GPIO
import ConfigParser

# Librairies local
from utilities import *
from RFID import *
from distance import *
from init import *

#############################################################################
# DECLARATION DES CONSTANTES

#Lecture du fichier config.ini
config = ConfigParser.ConfigParser()
config.read('config.ini')


BITRATE = config.getint('GPIO', 'BITRATE')
PINLED = config.getint('GPIO', 'PINLED')
GPIO_TRIGGER = config.getint('GPIO', 'GPIO_TRIGGER')
GPIO_ECHO = config.getint('GPIO', 'GPIO_ECHO')

portUsbRFID = config.get('RFID', 'portUsbRFID')

D_STREAM_NAME = config.get('ATTM2X', 'D_STREAM_NAME')
R_STREAM_NAME = config.get('ATTM2X', 'R_STREAM_NAME')
S_STREAM_NAME = config.get('ATTM2X', 'S_STREAM_NAME')
API_KEY = config.get('ATTM2X', 'API_KEY')

# preparation url pour la requests
data = 'apiKey=0cf6abec0e9ea8f4bd8f2d267bb2eacf&deviceId=07966069e262d4bd198568352c3a4318&streamId='
headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Accept': 'text/html'
    }
    
# Divers Variable
sendData_isActif = False
FondBLIisOK = False
TagRFID = ''
MARGE_FondBLI = 2
FondBLI = -1


#############################################################################
# DECLARATION DES METHODES

clear = lambda: os.system('clear') # clear() methode qui vide la console
now = datetime.datetime.now()      # now() methode qui retourne la date et l heure actuelle

def getFondBLI(): # retourne la valeur FondBLI
    global FondBLI
    return FondBLI

def setTagRFID(_TagRFID): # modifie la variable global TagRFID pour y mettre le tag passer en parametre
    global TagRFID
    TagRFID = _TagRFID

def sendData(type, valeur): # envoi des donnees a heroku, ( type : 'saturation' ou 'distance', valeur 'ex:12.3456')
    sendData_isActif = True
    SD_data = data
    global TagRFID
    
    if type == 'distance':
        if len(TagRFID) == 0:
        
            #creation chaine de caractere data
            SD_data += D_STREAM_NAME
            SD_data += '&valeur='
            SD_data += str(valeur)

            print "\n\033[01;35mO\033[m Envoi Distance"
            r = requests.post('http://testm2x.herokuapp.com/ws/postMessage.php', headers=headers, data=SD_data)
            
            print '\033[01;32m-\033[m'*40
            print "code de status : %d" % r.status_code
            print r.json
            print SD_data
            print '\033[01;32m-\033[m'*40
            
            SD_data = ' ' # clear data
            
        elif len(TagRFID) > 0:
            
            #creation chaine de caractere data
            SD_data += D_STREAM_NAME
            SD_data += ';'
            SD_data += R_STREAM_NAME
            SD_data += '&valeur='
            SD_data += str(valeur)
            SD_data += ';'
            SD_data += TagRFID
            
            print "\n\033[01;35mO\033[m Envoi Distance + RFID"
            r = requests.post('http://testm2x.herokuapp.com/ws/postMessage.php', headers=headers, data=SD_data)
        
            print '\033[01;32m-\033[m'*40
            print "code de status : %d" % r.status_code
            print r.json
            print SD_data
            print '\033[01;32m-\033[m'*40
            
            TagRFID = '' # clear TagRFID
            SD_data = '' # clear data
            
        else :
            print "Something wrong happened  with TagRFID"
            
    elif type == 'saturation':
            
            #creation chaine de caractere data
            SD_data += S_STREAM_NAME
            SD_data += '&valeur='
            SD_data += str(valeur)
            print ("\n\033[01;35mO\033[m Envoi du pourcentage de saturation!")
            
            r = requests.post('http://testm2x.herokuapp.com/ws/postMessage.php', headers=headers, data=SD_data)
            
            print '\033[01;32m-\033[m'*40
            print "code de status : %d" % r.status_code
            print r.json
            print SD_data
            print '\033[01;32m-\033[m'*40
            
            SD_data = ' ' # clear data
    
    else:
        print("type non reconnu")
        
    sendData_isActif = False

    
#############################################################################
# DECLARATION DES THREADS

class thread_lectureRFID(Thread): # Thread qui lit les tag RFID entrant sur la connexion serial

    def run(self):
        #Code a executer pendant l'execution du thread
        if __name__ == '__main__':
            
            while True:
                Debut = time.time()
                setTagRFID(lectureRFID(serialRFID, '%Y-%m-%d %H:%M:%S %Z', PINLED)) #Lecture du tag RFID
                Fin = time.time()
                if ((Fin - Debut) > 2):    
                    print '\n\033[01;32mOK\033[m RFID enregistre !' #Affiche la detection d'un tag RFID
                
                time.sleep(0.2) #ralenti le thread pour eviter de flood la console
                

class lectureDISTANCE(Thread): # Thread qui se charge de la gestion du capteur de distance ultrason
        
    def run(self):
        #Code a executer pendant l'execution du thread
        
        _FondBLI = getFondBLI()
        
        Debut = time.time()
        while True:
            e_WaitToSend = True # e pour evenement (envoi sur demande)
            r_WaitToSend = True # r pour repetition (envoi toute les 10s)
            Distance_OK = False
            
            while not Distance_OK:
                _distance = lectureDistance(16, 18)
                if not (_distance == -1):
                    Distance_OK = True
                else:
                    print("\033[01;31mX033[m Lecutre Distance : NaN")
            

            if (_distance > 3): # verifie si la valeur obtenu est entre le fond de la boite au lettre et la distance minimum recu par le capteur
                if (_distance < _FondBLI + MARGE_FondBLI):
                  #############################################################################
                  # ENVOI POURCENTAGE DE SATURATION                                           #
            
                    # envoi du pourcentage de saturation toute les 10s
                    Fin = time.time()
                    
                    if (Fin - Debut) >= 10: #ne detecte que si une lettre passe au dessus des 75%
                        
                        p_saturation = 100 - ((_distance*100)/_FondBLI) #calcul du pourcentage (p_ pour Pourcentage)
                        
                        while r_WaitToSend:
                            if sendData_isActif == False :
                                if p_saturation <= 0:
                                    p_saturation = 0
                                sendData('saturation', p_saturation)
                                r_WaitToSend = False
                                time.sleep(1)
                            else:
                                print "echec de l envoi, prochaine tentative dans 1 seconde"
                                time.sleep(1)
                                
                        Debut = time.time()
                        Fin = time.time()
                  #                                                                           #
                  #############################################################################
                    
                    
                  #############################################################################
                  # ENVOI DISTANCE                                                            #
          
                
                    if (_distance > _FondBLI - MARGE_FondBLI):
                        time.sleep(0.01)
                    if (_distance <= (_FondBLI*0.40)):
                        switchOnLed(PINLED)
                        
                        while e_WaitToSend:
                            if sendData_isActif == False :
                                sendData('distance', _distance)
                                e_WaitToSend = False
                                time.sleep(1)
                            else:
                                print "echec de l envoi, prochaine tentative dans 1 seconde"
                                time.sleep(1)
                    else:
                        switchOffLed(PINLED)
                  #                                                                           #
                  #############################################################################
                else:
                    print("\033[01;31mX\033[m distance > _FondBLI + marge")
                    time.sleep(0.2) #ralenti le thread pour eviter de flood la console
            else:
                print("\033[01;31mX\033[m distance < 3")
                time.sleep(0.2) #ralenti le thread pour eviter de flood la console

#############################################################################
# LIGNE EXECUTER AU LANCEMENT

clear()

#initialisation des diferents ports
BLI()
gpioSetup(GPIO_TRIGGER,GPIO_ECHO)
serialRFID = serialSetup(portUsbRFID, BITRATE)
FondBLI = lectureDistanceFondBLI(GPIO_TRIGGER, GPIO_ECHO)
done()


# Allow module to settle
time.sleep(0.5)

# Creation des threads
thread_LR = thread_lectureRFID()
thread_LD = lectureDISTANCE()

# Lancement des threads
thread_LR.start()
thread_LD.start()

# Attend que les threads se terminent
thread_LR.join()
thread_LD.join()
