from traveler import *
from system import * 
from road_map import *
from bus import *

def read(file:str):
    all_people = set()
    for line in open(file):

        splited = line.split()
        for i in range(int(splited[0])):
            all_people.add(Traveler.string(line,int(i)))
    return(all_people)

def init_bus_station(bus_stations, bus_stations_list):
    for station in bus_stations_list:
        bus_stations[station] = list()
    return bus_stations



all_people = read('People.txt')
time_limit = 100000
roads = Road_map({'A','B','C','D','E'})
roads.add_road('A','B',10)
roads.add_road('A','C',4)
roads.add_road('C','D',12)
roads.add_road('C','E',4)



bus_station = dict()
bus_station = init_bus_station(bus_station,roads.bus_stations)



system = System(all_people, roads, bus_station)
system.add_bus(Bus(10,1,1,30,'BACECA'))
system.add_bus(Bus(10,1,1,30,'DCEC'))
system.add_bus(Bus(10,1,1,30,'BED'))
system.play()