import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HomeHeadlineComponent } from './home-headline.component';

describe('HomeHeadlineComponent', () => {
  let component: HomeHeadlineComponent;
  let fixture: ComponentFixture<HomeHeadlineComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ HomeHeadlineComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(HomeHeadlineComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
