use rand::{Rng};
use sfml::graphics::{Color};
use crate::{MAX_ENTITIES};

#[derive(Clone)]
pub(crate) struct Vect2D {
    x: f64,
    y: f64,
}

impl Vect2D {
    fn new(x: f64, y: f64) -> Vect2D {
        Vect2D { x, y }
    }
}

#[derive(Eq, PartialEq)]
pub(crate) enum State {
    Healthy(Color),
    Ill(Color),
    Healed(Color),
    Dead(Color),
}

pub(crate) fn get_color(bla: fn(Color) -> State) -> Color {
    match bla(Color::default()) {
        State::Healthy(_) => Color::rgb(100, 100, 200),
        State::Ill(_) => Color::rgb(200, 100, 100),
        State::Healed(_) => Color::rgb(100, 200, 100),
        State::Dead(_) => Color::rgb(0, 0, 0),
    }
}

#[derive(Clone)]
pub(crate) struct Entity {
    position: Position,
    speed: Vect2D,
    max_height: f64,
    max_width: f64,
    pub(crate) state: fn(Color) -> State,
}


impl Entity {
    pub(crate) fn step(&mut self) {
        self.position.translate(&self.speed)
    }
    pub(crate) fn init_random(height: u32, width: u32, ill_chance: f64) -> Entity
    {
        let mut rng = rand::thread_rng();
        const MAX_SPEED: f64 = 1.0;
        const MIN_POSITION: u32 = 0;

        let state: fn(Color) -> State = if rng.gen_bool(ill_chance) { State::Ill } else { State::Healthy };

        Entity {
            position: Position::new(rng.gen_range(MIN_POSITION..width) as f64, rng.gen_range(MIN_POSITION..height) as f64),
            speed: Vect2D::new(rng.gen_range(-MAX_SPEED..MAX_SPEED) as f64, rng.gen_range(-MAX_SPEED..MAX_SPEED) as f64),
            max_height: height as f64,
            max_width: width as f64,
            state,
        }
    }

    pub(crate) fn update_state(&mut self) {
        if self.state == State::Healed { return; }
        if self.state == State::Dead { return; }
        if self.state == State::Healthy { return; }

        let rnd = rand::thread_rng().gen_range(0..100);
        if rnd <= 1 { self.state = State::Dead; } else if rnd <= 2 { self.state = State::Healed }
        // if Malade:
        //Random number
        // 0.2< = Death 0.4= heal
        // else nothing
    }

    pub(crate) fn spread(&self, entities: &mut Vec<Entity>) {
        for i in 0..MAX_ENTITIES as usize {
            if self.position.distance_to(entities[i].position) <= 100.0 {
                entities[i].get_ill();
            }
        }
    }

    pub(crate) fn get_ill(&mut self) {
        let rnd = rand::thread_rng().gen_range(0..100);
        if self.state == State::Healed { return; }
        if self.state == State::Dead { return; }
        if rnd <= 100 { self.state = State::Ill; }
    }

    pub(crate) fn check_outside(&mut self) {
        if self.position.x > self.max_width {
            self.position.x = 0.0;
        } else if self.position.x < 0.0 {
            self.position.x = self.max_width;
        }
        if self.position.y > self.max_height {
            self.position.y = 0.0;
        } else if self.position.y < 0.0 {
            self.position.y = self.max_height;
        }
    }
    pub(crate) fn get_position(&mut self) -> Position {
        self.position
    }
}


#[derive(Copy, Clone)]
pub(crate) struct Position {
    x: f64,
    y: f64,
}

impl Position {
    fn new(x: f64, y: f64) -> Position {
        Position { x, y }
    }

    fn translate(&mut self, v: &Vect2D) {
        self.x += v.x;
        self.y += v.y;
    }

    pub(crate) fn to_tuple(self) -> (f32, f32) {
        (self.x as f32, self.y as f32)
    }

    pub(crate) fn distance_to(self, position: Position) -> f64 {
        f64::sqrt(f64::powf(self.x - position.x, 2f64) + f64::powf(self.y - position.y, 2f64))
    }
}


#[cfg(test)]
mod test {
    #[test]
    fn test_null_vector(){
        let vector = Vect2D::new(0f64, 0f64);
        assert_eq!(vector.x, 0f64);
        assert_eq!(vector.y, 0f64);
    }

    #[test]
    fn test_unit_vector() {
        let vector = Vect2D::new(1f64, 1f64);
        assert_eq!(vector.x, 1f64);
        assert_eq!(vector.y, 1f64);
    }

    use crate::entity::{Position, Vect2D};

    #[test]
    fn test_origin_position() {
        let position = Position::new(0f64, 0f64);
        assert_eq!(position.x, 0f64);
        assert_eq!(position.y, 0f64);
    }

    #[test]
    fn test_unit_position() {
        let position = Position::new(1f64, 1f64);
        assert_eq!(position.x, 1f64);
        assert_eq!(position.y, 1f64);
    }

    #[test]
    fn test_translate_with_null_vector() {
        let mut position = Position::new(1f64, 1f64);
        let vector = Vect2D::new(0f64, 0f64);
        position.translate(&vector);
        assert_eq!(position.x, 1f64);
        assert_eq!(position.y, 1f64);
    }

    #[test]
    fn test_translate_with_unit_vector() {
        let mut position = Position::new(1f64, 1f64);
        let vector = Vect2D::new(2f64, 2f64);
        position.translate(&vector);
        assert_eq!(position.x, 3f64);
        assert_eq!(position.y, 3f64);
    }

    #[test]
    fn test_to_tuple() {
        let position = Position::new(1f64, 1f64);
        assert_eq!(position.to_tuple(), (1f32, 1f32));
    }

    #[test]
    fn test_zero_distance_to() {
        let first_position = Position::new(1f64, 1f64);
        let second_position = Position::new(1f64, 1f64);
        assert_eq!(first_position.distance_to(second_position), 0f64)
    }

    #[test]
    fn test_unit_distance_to() {
        let first_position = Position::new(1f64, 1f64);
        let second_position = Position::new(0f64, 1f64);
        assert_eq!(first_position.distance_to(second_position), 1f64)
    }
}
