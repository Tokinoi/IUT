from traveler import * 
from bus import * 
from trajet import *
from waiter import *

 #TODO : Tranformer tout les people en waiter dès le début. 



class System():

    road_map = None
    bus = list()

    def __init__(self, people, roads ,bus_stations):
        self.time = int(0)
        System.road_map = roads
        self.stations = dict()
        for el in bus_stations.keys():
            self.stations[el] = list()
        self.people = list(people)
        #self.add_people()

    def play(self):
        while self.time < 602:
            print('*************************TICK NUMERO :'+str(self.time) +'*******************************')
            self.do_one_step()
            self.time+=1


    def do_one_step(self):
        self.add_people()
        self.move_bus()


    def get_time_to(first_station,second_station):
        return System.road_map.get_distance(first_station,second_station)

    def add_people(self):
        last_people = None
        i = 0
        while i < len(self.people):


            if last_people == self.people[i]:
                i+=1
                continue
            else:
                last_people = self.people[i]
            waiter = self.people[i].get_waiter_allee(self.time)
            if waiter.is_time(self.time):
                self.stations[waiter.start].append(waiter)
                print('[Allée] Arrivé de ' + waiter.log())
                self.stations[waiter.start].sort()
            waiter = self.people[i].get_waiter_retour(self.time)

            if waiter.is_time(self.time):
                self.stations[waiter.start].append(waiter)
                print('[Retour] Arrivé de ' + waiter.log())
                self.stations[waiter.start].sort()
                self.people.pop(i)
                i-=1
            i += 1

    def move_bus(self):
        for bus in self.bus:
            bus.action(self.stations[bus.get_heading()])

    def add_bus(self,bus):
        self.bus.append(bus)


    def get_travel(start,end):
        #Obtenir les arrêt à faire.
        #Si direct possible -> Renvoyé direct 
        for bus in System.bus:
            try: 
                bus.road.index(start)
                bus.road.index(end)
                return start+end
            except: 
                return 