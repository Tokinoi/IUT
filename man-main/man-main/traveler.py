from trajet import * 
from waiter import *

class Traveler():


    def __init__(self, nom: str, allee : Trajet, retour : Trajet):
        self.nom = nom 
        self.allee = allee 
        self.retour = retour 
        print(self)

    
    def string(line: str, number : int):
        line = line.split()
        return Traveler(nom = line[1]+ str(number).zfill(4), allee = Trajet(line[2],line[3]), retour = Trajet(line[4],line[5]))
    
    def want_to_leave(self, bus_station):
        return self.allee.fin == bus_station or self.retour.fin == bus_station

    def __str__(self):
        return self.nom +"\n"+ str(self. allee) + str(self.retour) 

    def go_to_bus_station(self,time: int):
        return self.retour.is_time(time) or self.allee.is_time(time)

    def get_waiter_retour(self,time :int):
        return Waiter.init_from_trav(self,True)
    

    def get_waiter_allee(self,time :int):
        return Waiter.init_from_trav(self,False)
        