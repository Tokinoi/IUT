import heapq

class Road_map:

    def __init__(self, bus_stations: set):
        self.bus_stations = bus_stations
        self.road = {station: [] for station in bus_stations}

    def add_road(self, first_station, second_station, length: int):
        self.road[second_station].append((first_station, length))
        self.road[first_station].append((second_station, length))

    def get_distance(self, start_station, end_station):
        # Dijkstra's algorithm
        if start_station not in self.bus_stations or end_station not in self.bus_stations:
            return float('inf')  # Return infinity for unreachable stations

        distances = {station: float('inf') for station in self.bus_stations}
        distances[start_station] = 0

        priority_queue = [(0, start_station)]

        while priority_queue:
            current_distance, current_station = heapq.heappop(priority_queue)

            if current_distance > distances[current_station]:
                continue

            for neighbor, weight in self.road[current_station]:
                distance = current_distance + weight
                if distance < distances[neighbor]:
                    distances[neighbor] = distance
                    heapq.heappush(priority_queue, (distance, neighbor))

        return distances[end_station]