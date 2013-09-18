//
//  CateViewController.h
//  ipapp
//
//  Created by kelaocai on 13-9-18.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "UIFolderTableView.h"
#import "CateTableCell.h"


@interface CateViewController : UIViewController<UITableViewDelegate,UITableViewDataSource>
@property (strong, nonatomic) IBOutlet UIFolderTableView *cateTableView;
@property(strong,nonatomic) NSArray *cates;


@end
