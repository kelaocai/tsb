//
//  PostListViewController.h
//  ipapp
//
//  Created by kelaocai on 13-10-1.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <UIImageView+WebCache.h>

@interface PostListViewController : UITableViewController
@property (strong,nonatomic)NSString *tid;
@property (strong,nonatomic)NSArray *posts;
@property (retain, nonatomic) IBOutlet UITableView *postTableView;

@end
