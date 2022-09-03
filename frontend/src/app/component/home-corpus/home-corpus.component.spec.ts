import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HomeCorpusComponent } from './home-corpus.component';

describe('HomeCorpusComponent', () => {
  let component: HomeCorpusComponent;
  let fixture: ComponentFixture<HomeCorpusComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ HomeCorpusComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(HomeCorpusComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
