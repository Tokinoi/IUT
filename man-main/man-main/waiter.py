from traveler import * 
import system as sy
class Waiter():


    def __init__(self, nom: str, start:str, ending:str, time:int):
        self.nom    = nom 
        self.start  = start
        self.end = ending
        self.time   = time 
        self.bus_travel = self.init_bus_travel(self.start,self.end)

    def __lt__(self, other):
        if self.time < other.time: 
            return True
        elif self.time > other.time: 
            return False
        if self.nom > other.nom:
            return False
        return True
        

    def __str__(self):
        return self.nom +"\t"+ str(self. start) + str(self.end) + "\t" + str(self.time) 
    
    def log(self):
        return self.nom +" à l'arret "+ self.start

    def init_from_trav(traveler, retour: bool):
        if not(retour):
            return Waiter(traveler.nom,traveler.allee.start,traveler.allee.end,traveler.allee.heure)
        return Waiter(traveler.nom,traveler.retour.start,traveler.retour.end,traveler.retour.heure)

    def is_time(self,time):
        return int(self.time) == (time)
    

    def init_bus_travel(self,start,end):
        return sy.System.get_travel(start,end)