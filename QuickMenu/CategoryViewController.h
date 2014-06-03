//
//  CategoryViewController.h
//  Menyou
//
//  Created by Noah Martin on 5/5/14.
//  Copyright (c) 2014 Noah Martin. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Categories.h"
#import "DishTableViewCell.h"

@interface CategoryViewController : UIViewController <UITableViewDataSource, UITableViewDelegate, DishTableViewDelegate>
@property (weak, nonatomic) IBOutlet UILabel *titleLabel;

@property (weak, nonatomic) Categories* category;
@property NSString* restaurant;

-(void)updateTableView;

@end
