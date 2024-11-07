import system as sy

# TODO : Faire que les voyageurs regarde si le bus va là où il veut aller 


class Bus:

    def __init__(self, capacity : int, filling_speed : float , num : int, denom :int,  road):
        self.capacity = capacity                #Traveler Limit in the bus
        self.fillin_speed = filling_speed       #Number of Traveler who can get in or get out at the same time
        self.speed = num
        self.modulator = denom                           #Number of meter traveled each time
        self.road = road                        #All the bus stations he do
        self.current_location = 0               #Index of the current bus_station it is heading
        self.customer = set()                   #All the customer in the bus
        self.is_moving = True                     #Is the bus currently moving
        self.change_heading()                   #the distance beetween the current bus station and the one it is heading
        self.already_moved_distance = 0         #How much meter did it already reach


    def action(self,customer):
        if self.is_moving:
            self.move()
            return 
        if self.move_traveler(customer):
            self.start_moving()
        

    def move(self):
        self.already_moved_distance += self.speed
        if self.move_distance <= self.already_moved_distance:
            self.is_move = False
            self.already_moved_distance = 0
            self.change_heading()

    def change_heading(self):
        self.log()
        self.move_distance = (sy.System.get_time_to(self.road[self.current_location],self.road[self.current_location+1%(len(self.road)-1)])* self.modulator)
        self.current_location= (self.current_location+1)%(len(self.road)-1)

    def add(self,customers):
        for i in range(self.fillin_speed):
            if len(customers) != 0 and len(self.customer) != self.capacity:
                self.customer.add(customers.pop())
                continue
            return False
        return True 

    def remove(self):
        leave = next(ele.want_to_leave() for ele in list(self.customer))
        if leave :
            self.customer.remove(leave)
            if self.capacity > len(self.customer):
                return True 
        return False
    
    def get_heading(self):
        return self.road[self.current_location]
    
    def log(self):
        print('Bus :'+ self.road )
        print("\tArrivé à l'arrêt :" + str(self.road[self.current_location]))


    def __str__(self):
        return self.get_heading() +" "+ str(self.already_moved_distance)