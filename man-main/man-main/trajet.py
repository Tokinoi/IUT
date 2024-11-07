class Trajet(): 

    def __init__(self, heure:int, trajet:str):
        self.heure = heure
        self.start = trajet[0]
        self.end = trajet[1]


    def __str__(self):
        return "heure :" + self.heure + "s\ndébut : " + self.start + "\nfin : " +self.end +"\n"
    
    def is_time(self,time : int):
        return int(self.heure) == time
    